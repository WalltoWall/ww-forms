var l=Object.defineProperty;var i=(n,t)=>l(n,"name",{value:t,configurable:!0});const u=i(()=>{document.querySelectorAll(".ww-form").forEach(t=>{t.addEventListener("submit",a=>{a.preventDefault();const e=t.dataset.sitekey??"";e?grecaptcha.ready(function(){grecaptcha.execute(e,{action:"submit"}).then(function(s){r(t,s)})}):r(t)})})},"initForms");u();const r=i(async(n,t=null)=>{const a=n.querySelector("button[type=submit]");a.disabled=!0;const e=new FormData,s=Object.fromEntries(new FormData(n));n.querySelectorAll("input[type=file]").forEach(o=>{const c=o.files;c&&e.append(o.name,c[0])}),e.append("action","submit_form_data"),e.append("formId",n.dataset.id??""),e.append("submission",JSON.stringify(s)),t&&e.append("recaptchaToken",t);const p=await(await fetch("/wp-admin/admin-ajax.php",{method:"post",body:e})).text(),d=n.parentElement;d.innerHTML=p,a.disabled=!1},"submitData");
