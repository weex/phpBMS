<?php
class VariableStream
{
	// Stream handler to read from global variables
	var $varname;
	var $position;

	function stream_open($path, $mode, $options, &$opened_path)
	{
		$url = parse_url($path);
		$this->varname = $url['host'];
		if(!isset($GLOBALS[$this->varname]))
		{
			trigger_error('Global variable '.$this->varname.' does not exist', E_USER_WARNING);
			return false;
		}
		$this->position = 0;
		return true;
	}

	function stream_read($count)
	{
		$ret = substr($GLOBALS[$this->varname], $this->position, $count);
		$this->position += strlen($ret);
		return $ret;
	}

	function stream_eof()
	{
		return $this->position >= strlen($GLOBALS[$this->varname]);
	}
}

class MEM_IMAGE extends FPDF
{
	// (c) Xavier Nicolay
	// V1.0 : 2004-01-17
	
	//
	// CONSTRUCTOR
	//
	function MEM_IMAGE($orientation='P',$unit='mm',$format='A4')
	{
		$this->FPDF($orientation, $unit, $format);
		//Register var stream protocol (requires PHP>=4.3.2)
		if(function_exists('stream_wrapper_register'))
			stream_wrapper_register('var','VariableStream');
	}

	//
	// PRIVATE FUNCTIONS
	//
	function _readstr($var, &$pos, $n)
	{
		//Read some bytes from string
		$string = substr($var, $pos, $n);
		$pos += $n;
		return $string;
	}
	
	function _readstr_int($var, &$pos)
	{
		//Read a 4-byte integer from string
		$i =ord($this->_readstr($var, $pos, 1))<<24;
		$i+=ord($this->_readstr($var, $pos, 1))<<16;
		$i+=ord($this->_readstr($var, $pos, 1))<<8;
		$i+=ord($this->_readstr($var, $pos, 1));
		return $i;
	}

	function _parsemempng($var)
	{
		$pos=0;
		//Check signature
		$sig = $this->_readstr($var,$pos, 8);
		if($sig != chr(137).'PNG'.chr(13).chr(10).chr(26).chr(10))
			$this->Error('Not a PNG image');
		//Read header chunk
		$this->_readstr($var,$pos,4);
		$ihdr = $this->_readstr($var,$pos,4);
		if( $ihdr != 'IHDR')
			$this->Error('Incorrect PNG Image');
		$w=$this->_readstr_int($var,$pos);
		$h=$this->_readstr_int($var,$pos);
		$bpc=ord($this->_readstr($var,$pos,1));
		if($bpc>8)
			$this->Error('16-bit depth not supported: '.$file);
		$ct=ord($this->_readstr($var,$pos,1));
		if($ct==0)
			$colspace='DeviceGray';
		elseif($ct==2)
			$colspace='DeviceRGB';
		elseif($ct==3)
			$colspace='Indexed';
		else
			$this->Error('Alpha channel not supported: '.$file);
		if(ord($this->_readstr($var,$pos,1))!=0)
			$this->Error('Unknown compression method: '.$file);
		if(ord($this->_readstr($var,$pos,1))!=0)
			$this->Error('Unknown filter method: '.$file);
		if(ord($this->_readstr($var,$pos,1))!=0)
			$this->Error('Interlacing not supported: '.$file);
		$this->_readstr($var,$pos,4);
		$parms='/DecodeParms <</Predictor 15 /Colors '.($ct==2 ? 3 : 1).' /BitsPerComponent '.$bpc.' /Columns '.$w.'>>';
		//Scan chunks looking for palette, transparency and image data
		$pal='';
		$trns='';
		$data='';
		do
		{
			$n=$this->_readstr_int($var,$pos);
			$type=$this->_readstr($var,$pos,4);
			if($type=='PLTE')
			{
				//Read palette
				$pal=$this->_readstr($var,$pos,$n);
				$this->_readstr($var,$pos,4);
			}
			elseif($type=='tRNS')
			{
				//Read transparency info
				$t=$this->_readstr($var,$pos,$n);
				if($ct==0)
					$trns=array(ord(substr($t,1,1)));
				elseif($ct==2)
					$trns=array(ord(substr($t,1,1)),ord(substr($t,3,1)),ord(substr($t,5,1)));
				else
				{
					$pos=strpos($t,chr(0));
					if(is_int($pos))
						$trns=array($pos);
				}
				$this->_readstr($var,$pos,4);
			}
			elseif($type=='IDAT')
			{
				//Read image data block
				$data.=$this->_readstr($var,$pos,$n);
				$this->_readstr($var,$pos,4);
			}
			elseif($type=='IEND')
				break;
			else
				$this->_readstr($var,$pos,$n+4);
		}
		while($n);
		if($colspace=='Indexed' and empty($pal))
			$this->Error('Missing palette in '.$file);
		return array('w'=>$w,
					 'h'=>$h,
					 'cs'=>$colspace,
					 'bpc'=>$bpc,
					 'f'=>'FlateDecode',
					 'parms'=>$parms,
					 'pal'=>$pal,
					 'trns'=>$trns,
					 'data'=>$data);
	}
  
	/********************/
	/* PUBLIC FUNCTIONS */
	/********************/
	function MemImage($data, $x, $y, $w=0, $h=0, $link='')
	{
		//Put the PNG image stored in $data
		$id = md5($data);
		if(!isset($this->images[$id]))
		{
			$info = $this->_parsemempng( $data );
			$info['i'] = count($this->images)+1;
			$this->images[$id]=$info;
		}
		else
			$info=$this->images[$id];
	
		//Automatic width and height calculation if needed
		if($w==0 and $h==0)
		{
			//Put image at 72 dpi
			$w=$info['w']/$this->k;
			$h=$info['h']/$this->k;
		}
		if($w==0)
			$w=$h*$info['w']/$info['h'];
		if($h==0)
			$h=$w*$info['h']/$info['w'];
		$this->_out(sprintf('q %.2f 0 0 %.2f %.2f %.2f cm /I%d Do Q',$w*$this->k,$h*$this->k,$x*$this->k,($this->h-($y+$h))*$this->k,$info['i']));
		if($link)
			$this->Link($x,$y,$w,$h,$link);
	}
	
	function GDImage($im, $x, $y, $w=0, $h=0, $link='')
	{
		//Put the GD image $im
		ob_start();
		imagepng($im);
		$data = ob_get_contents();      
		ob_end_clean();
		$this->MemImage($data, $x, $y, $w, $h, $link);
	}

}
?>
