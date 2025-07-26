document.addEventListener("DOMContentLoaded",function(){
  const form=document.getElementById("bdDomainCheckerForm");
  if(!form) return;
  const input=document.getElementById("domainInput");
  const result=document.getElementById("bdResultContainer");

  form.addEventListener("submit",function(e){
    e.preventDefault();
    const name=input.value.trim();
    if(!name){
      result.innerHTML='<div class="domain-error-msg">⚠️ Please enter a domain name!</div>';
      return;
    }
    result.innerHTML="<div class='loading'>⏳ Checking all extensions...</div>";
    fetch(bdChecker.ajax_url,{
      method:"POST",
      headers:{"Content-Type":"application/x-www-form-urlencoded"},
      body:"action=bddc_check&domain="+encodeURIComponent(name)
    })
    .then(r=>r.text())
    .then(data=>{ result.innerHTML=data; })
    .catch(err=>{
      console.error(err);
      result.innerHTML='<div class="domain-error-msg">❌ AJAX Failed!</div>';
    });
  });
});
