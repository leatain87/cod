(function($){
    $(function() {
        function log(str){console.log(str)}
        function loadBonus(quanlity, options){
            
            var group = quanlity.parents('.rsform-block'),
                select = group.prev().find('select')
            ;
            if(group.find('.productBonus').length < 1) group.append('<div class="productBonus"></div>');
            if(quanlity.val() > 0 && $.isNumeric(quanlity.val()) && select.val()){
                $.each(options, function(k, v){
                    if(k == select.val()){
                        group.find('.productBonus').html('<span>Bonus Item: </span><span>'+quanlity.val()+' x '+v+'</span>');
                    }
                })
            }else group.find('.productBonus').html('');
        }
       
       function addModels(options){
           var data = [],
               i = 0
           ;
           $('input[id^=Quantity]').each (function(){
               var  quanlity = $(this),
                    group = quanlity.parents('.rsform-block'),
                    select = group.prev().find('select')
                ; 
                if(quanlity.val() > 0 && $.isNumeric(quanlity.val()) && select.val()){
                    data[i] = {};
                    $.each(options, function(k, v){
                        if(k == select.val()){
                            data[i].model = select.val();
                            data[i].quantity = quanlity.val();
                            data[i].bonus = v;
                        }
                    });
                    i++;
                }
           })
           $('#listModels').val(JSON.stringify(data));
       }
       function newModels(options, mod, quan){
           var  quan = quan || '',
                mod = mod || '',
                theBefore = '.rsform-block-addmoremodels',
                display = (mod)? '' : 'none',
                bonus = (quan)? '<div class="productBonus"><span>Bonus Item: </span><span>'+quan+' x '+mod+'</span></div>' : '';
            ;
           $.each(options, function(i, k){ 
               if(i == mod) optionHtml += '<option selected="selected" value="'+i+'">'+i+'</option>';
               else optionHtml += '<option value="'+i+'">'+i+'</option>';
           })
           var xhtml = '<div class="uk-form-row rsform-block rsform-block-purchase-models'+models+' jomkungfu jvFields"><div class="uk-form-controls formControls"><select name="form[Purchase Models'+models+'][]" id="Purchase Models'+models+'" class="rsform-select-box knigherrant">'+optionHtml+'</select><span class="formValidation"><span id="component102" class="formNoError">Invalid Input</span></span></div></div><div style="display:'+display+'" class="uk-form-row rsform-block rsform-block-quantity'+models+' jvFieldsQuantily"><div class="uk-form-controls formControls"><input placeholder="Quantity" value="'+quan+'" size="20" name="form[Quantity'+models+']" id="Quantity'+models+'" class="rsform-input-box jvQuantity" type="text"/> <span class="formValidation"><span id="component103" class="formNoError">Invalid Input</span></span></div>'+bonus+'</div>';
            $(theBefore).before(xhtml);
            $('select.knigherrant').change(function(){
                var quanlity = $(this).parents('.jomkungfu');
                if($(this).val() != '') quanlity.next().show();
                else quanlity.next().hide();
                loadBonus(quanlity.next().find('.jvQuantity'), options);
            });
            $('input[id^=Quantity]').bind('keyup keydown change', function(){loadBonus($(this), options)});
       }
       jQuery('[id^=Purchase]').change(function(){
           var quanlity = $(this).parents('.rsform-block');
           loadBonus(quanlity.next().find('[id^=Quantity]'), options);
       })
       
        //AIzaSyDg5PpsubX_1oDy6iYJZWMT2XEMxt3uoTU
        var invoice = 1,
            models = 0,
            upload = 0,
            optionHtml = '<option value=""> - Select Model - </option>',
            jsonList = {}
        ;
        
         var req = {
                types: ['geocode'],
                componentRestrictions : { country: 'au' }
            },
            componentForm = {
                street_number: 'short_name',
                route: 'long_name',
                locality: 'long_name',
                administrative_area_level_1: 'short_name',
                country: 'long_name',
                postal_code: 'short_name'
            },
            input = document.getElementById('StreetAddress')
        ;
        //var options = { componentRestrictions: defaultBounds, types: ['establishment'] };
        autocomplete = new google.maps.places.Autocomplete(input, req);
        autocomplete.addListener('place_changed', fillInAddress);
        function fillInAddress() {
            // Get the place details from the autocomplete object.
            var place = autocomplete.getPlace(),
                address =''
            ;
            // Get each component of the address from the place details
            // and fill the corresponding field on the form.
            
            for (var i = 0; i < place.address_components.length; i++) {
                var addressType = place.address_components[i].types[0];
                    if (componentForm[addressType]) {
                        var val = place.address_components[i][componentForm[addressType]];
                        if(addressType == 'postal_code'){
                            $('#Postcode').val(val);
                        }else if(addressType == 'locality'){
                            $('#Suburb').val(val);
                        }else if(addressType == 'street_number' || addressType == 'route'){
                            address += val + ' ';
                        }
                    }
            }
            $('#StreetAddress').val(address);
        }
        
        
        $('#AddMoreInvoice').click(function(){
           for (invoice = 2; invoice < 11; invoice ++){
               var groupfields = '.rsform-block-invoicedate'+invoice;
               if(!$(groupfields).is(':visible')){
                   $(groupfields).show(); 
                   $(groupfields).prev().show();
                   break; 
               }
           }
        })
        for (ii = 2; ii < 11; ii ++){
            var groupfields = '.rsform-block-invoicedate'+ii;
            if($(groupfields).find('input[id^=txtjQcal13]').val() || $(groupfields).prev().find('[id^=Invoice]').val() ){
                $(groupfields).show();
                $(groupfields).prev().show();
            } 
        }
        var options = {
            'DLX2064T'	: 'DJR187Z',		
            'DLX2092M'	: 'DJR187Z',		
            'DLX2092T'	: 'DJR187Z',		
            'DLX2092G'	: 'DJR187Z',		
            'DLX2176M'	: 'DJR187Z',		
            'DLX2176T'	: 'DJR187Z',		
            'DLX2185T'	: 'DJR187Z',		
            'DLX2214TJ'	: 'DJR187Z',		
            'DLX2214T'	: 'DJR187Z',		
            'DLX3054T'	: 'DJR187Z',		
            'DLX3055T'	: 'DJR187Z',		
            'DLX3056T'	: 'DJR187Z',		
            'DLX3061T'	: 'DJR187Z',		
            'DLX3062T'	: 'DJR187Z',		
            'DLX3072T'	: 'DJR187Z',		
            'DLX3073T'	: 'DJR187Z',		
            'DLX2204M'	: 'DTW285Z',
            'DLX5027T'	: 'DTW285Z',
            'DLX5028T'	: 'DTW285Z',
            'DLX5029T'	: 'DTW285Z',
            'DLX5030T'	: 'DTW285Z',
            'DLX5031T'	: 'DTW285Z',
            'DLX6051T'	: 'DTW285Z',
            'DLX6061T'	: 'DTW285Z',
            'DLX6062T'	: 'DTW285Z',
            'LXT613'	: 'DTW285Z',
            'LXT614'	: 'DTW285Z'
        };
        
        $('input[id^=Quantity]').bind('keyup keydown change', function(){loadBonus($(this),options)});
       var lists =  $('#listModels').val();
       if(lists.length > 11) var jsonList = JSON.parse(lists) || '';
       if(jsonList){
           loadBonus($('#Quantity'), options);
           $.each(jsonList, function(i, key){
               if(i > 0){
                    newModels(options, key.model, key.quantity);
                    models++;
                }
             })
        }
       $('#AddMoreModels').click(function(){
            models++;
            if(models >= 10){
                alert('Add More Models - Max 10 Product');
                return;
            }
            newModels(options);
            
       })
       
       $('.rsform-block-submit-invoice').find('input[type="radio"]').each(function(){
           if($(this).is(':checked')){
               if($(this).val() == 'Upload File'){
                   $('.rsform-block-addmorefile .formControls').show();
               }else $('.rsform-block-addmorefile .formControls').hide();
           }
       })
       
       $('.rsform-block-submit-invoice').find('input[type="radio"]').click(function(){
           if($(this).is(':checked') && $(this).val() == 'Upload File') $('.rsform-block-addmorefile .formControls').show();
           else $('.rsform-block-addmorefile .formControls').hide();
       })
       $('#AddMoreFile').click(function(){
           upload++;
           var html = '<div class="uk-form-controls formControls"><input name="form[Upload File'+upload+']" id="Upload File'+upload+'" class="rsform-upload-box" type="file"> <span class="formValidation"><span id="component87" class="formNoError">Invalid Input</span></span></div>';
           $('.rsform-block-upload-file').append(html);
       })
       
       
       $('#SubmitClaim').attr('type','button');
       $('#SubmitClaim').click(function(){
           addModels(options);
           var  error = [],
                mobile = $('#Mobile'),
                intRegex = /^\d+$/
            ;
            if(mobile.val()){
                if(intRegex.test(mobile.val()) && mobile.val().length == 10) {
               }else error.push(mobile);
            }
           
           if(error.length > 0){
                mobile.removeClass('rsform-error');
                $('html, body').animate({
                    scrollTop: jQuery('#Suburb').offset().top
                }, 500);
              
               $(error).each(function(i, input){
                   input.addClass('rsform-error');
               })
           }else{
               $('#bl-redeem').submit();
           }
       })
       
      
    });
})(jQuery)