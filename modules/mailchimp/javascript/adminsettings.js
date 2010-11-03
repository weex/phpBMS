mailchimpSettings = {
    
    apikeyChanged:function(){
        
        var apikey_changed = getObjectFromID("apikey_changed");
        
        if(apikey_changed.value != 1)
            apikey_changed.value = 1;
        
    },//end function
    
    listIdChanged:function(){
        var listid_changed = getObjectFromID("listid_changed");
        
        if(listid_changed.value != 1)
            listid_changed.value = 1;
    }//end function
    
}

/* OnLoad Listner ---------------------------------------- */
/* ------------------------------------------------------- */
connect(window, "onload", function(){
   
   var mailchimp_apikey = getObjectFromID("mailchimp_apikey");
   connect(mailchimp_apikey, "onchange", mailchimpSettings.apikeyChanged);
   
   var mailchimp_list_id = getObjectFromID("mailchimp_list_id");
   connect(mailchimp_list_id, "onchange", mailchimpSettings.listIdChanged);
    
});