jQuery(document).ready(function($){

    $('#bd-domain-submit').on('click', function(){

        let domainName = $('#bd-domain-input').val().trim();

        if(domainName === ''){
            $('#bd-domain-result').html('❌ Please enter a domain name.');
            return;
        }

        // লোডিং দেখাও
        $('#bd-domain-result').html('<div class="loading">⏳ Checking all BD extensions...</div>');

        $.post(bdAjax.ajaxurl, {
            action: 'bdc_check_domain',
            name: domainName,
            security: bdAjax.nonce
        }, function(response){
            if(response.success){

                let html = '';
                response.data.results.forEach(function(item){
                    html += `<div class="bdc-item">${item.status}</div>`;
                });

                $('#bd-domain-result').html(html);

            } else {
                $('#bd-domain-result').html('⚠️ Server error!');
            }
        }).fail(function(xhr, status, error){
            console.error("AJAX Error:", status, error);
            $('#bd-domain-result').html('⚠️ AJAX Failed. See console.');
        });

    });

});
