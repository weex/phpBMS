onchange = {

    /**
    * toggleOnchange
    * @param fieldName {string} The changed field's id.
    */
    toggleOnchange: function(e){

        var mainField = e.src();
        var hiddenField = getObjectFromID(mainField.id+"_changed");

        if(hiddenField)
            if(hiddenField.value != 1)
                hiddenField.value = 1;

    }//end method

}//end object
connect(window,"onload",function(){
    var i;
    var field;
    for(i = 0; i < onchangeArray.length; i++){

        field = getObjectFromID(onchangeArray[i]);
        connect(field, "onchange", onchange.toggleOnchange);

    }//end for
});