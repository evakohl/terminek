(function ($, window, document) {
    'use strict';
    // execute when the DOM is ready
    $(document).ready(function () {
        // js 'change' event triggered on the tek_add_date Button
        $('#tek_add_date').on('click', function () {
           // Add new set of date fields
           const n = $( '.tek_date_box' ).length;
           let html = '';
           html += '<div class="tek_date_box tek_date_box_' + n + '">';
           html += '<label for="tek_start_date_' + n + '">Start date</label>' +
                    '<input type="text" class="tek_date_input" id="tek_start_date_' + n + '" name="tek_start_date_' + n + '" value="" size="25" />' +
                    '<label for="tek_end_date_' + n + '">End date</label>' +
                    '<input type="text" class="tek_date_input" id="tek_end_date_' + n + '" name="tek_end_date_' + n + '" value="" size="25" />';
           if(tek_admin_script.showloc){
                html += '<label for="tek_location_' + n + '">Location</label>' +
                    '<input type="text" class="tek_date_input" id="tek_location_' + n + '" name="tek_location_' + n + '" value="" size="25" />';
           }
           if(tek_admin_script.showcust){
                html += '<label for="tek_custom_' + n + '">' + tek_admin_script.labelcust + '</label>' +
                    '<input type="text" class="tek_date_input" id="tek_custom_' + n + '" name="tek_custom_' + n + '" value="" size="25" />';
           }
           if(tek_admin_script.showcust2){
            html += '<label for="tek_custom2_' + n + '">' + tek_admin_script.labelcust2 + '</label>' +
                '<input type="text" class="tek_date_input" id="tek_custom2_' + n + '" name="tek_custom2_' + n + '" value="" size="25" />';
       }


           html += '</div>';

           $('#tek_date_container').append(html);
        });

      // check date if date field is left
         $('.tek_date_input').on('change', function () {
           // Check input for valid date
            const msg = '<div class="tek_error_msg">' +
                           '<p>' +
                           'This date may not be recognised properly. Please use DD.MM.YYYY or DD-MM-YYYY.' +
                           '</p>' +
                         '</div>';
            //if($(this).val() != "")
            let input = this.value;
            let date = new RegExp('^[0-9]{1,2}(\.|\-)[0-9]{1,2}(\.|\-)[0-9]{4}$');

            if (!date.test(input)) {
                $(this).addClass('tek_error');
                $(this).after(msg);
            }else{
                $(this).removeClass('tek_error');
                $(this).next('div.tek_error_msg').remove();
            }
            

            
            

         });

    });


   // $(document).ready(function () {

   //      // check date if date field is left
   //      $('.tek_date_input').on('change', function () {
   //         // Check input for valid date
   //         const msg = '<div class="tek_error">' +
   //                        '<p>' +
   //                        'This date may not be recognised. Please use DD.MM.YYYY or DD-MM-YYYY for best results.' +
   //                        '</p>' +
   //                      '</div>';
   //         //if($(this).val() != "")
   //          $('this').append(msg);

   //      });


   //  });
}(jQuery, window, document));