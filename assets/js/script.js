jQuery(document).ready(function($){
    $('#bd-domain-submit').on('click', function(){
        let domainName = $('#bd-domain-input').val().trim();

        if(domainName === ''){
            $('#bd-domain-result').html('❌ Please enter a domain name');
            return;
        }

        $('#bd-domain-result').html('⏳ Checking...');

        $.post(bdAjax.ajaxurl, {
            action: 'bdc_check_domain',
            name: domainName,
            security: bdAjax.nonce
        }, function(response){
            if(response.success){
                let html = '<ul>';
                response.data.results.forEach(function(item){
                    html += '<li>' + item.status + '</li>';
                });
                html += '</ul>';
                $('#bd-domain-result').html(html);
            } else {
                $('#bd-domain-result').html('⚠️ ' + (response.data.message || 'Server returned error'));
            }
        }).fail(function(xhr, status, error){
            console.error("❌ AJAX Error:", status, error);
            $('#bd-domain-result').html('⚠️ AJAX Failed. See console.');
        });
    });
});
