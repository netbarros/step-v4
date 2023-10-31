
function parseJwt (token) {
    var base64Url = token.split('.')[1];
    var base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
   var jsonPayload = decodeURIComponent(atob(base64).split('').map(function(c) {
     return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
   }).join(''));
 
   return JSON.parse(jsonPayload);
 };
 
   function handleCredentialResponse(response) {
      // decodeJwtResponse() is a custom function defined by you
      // to decode the credential response.
      const responsePayload = parseJwt(response.credential);
 
      console.log("ID: " + responsePayload.sub);
      console.log('Full Name: ' + responsePayload.name);
      console.log('Given Name: ' + responsePayload.given_name);
      console.log('Family Name: ' + responsePayload.family_name);
      console.log("Image URL: " + responsePayload.picture);
      console.log("Email: " + responsePayload.email);
 
 
      // pega as variáveis retornadas do google e verifica se o usuário já está registrado no sistema
      // 1 se o mesmo email estiver no step, mas o user não estiver ativo, retorna erro de acesso de login inativo.
      // 2 se o usuário estiver no step e não estiver no google, vincula o usuário do google ao step automaticamente e redireciona para o dashboard
      // 3 se o usuário já existir no step e com login do google vinculado:  redireciona para o dashboard
 
             
         var caminho = "./crud/login/valida-login-google.php";
         $.ajax({
             type: "POST",
             url: caminho,
             data: {
                 acao:'valida-google',
                 id:responsePayload.sub,
                 nome_completo:responsePayload.name, 
                 primeiro_nome:responsePayload.given_name,
                 sobre_nome:responsePayload.family_name,
                 foto:responsePayload.picture,
                 email:responsePayload.email
             },
             dataType: "json",
             cache: false,
 
 
             success: function (data) {
 
                 console.log(data);
 
                 console.log(data.codigo)
 
 
                 if (data.codigo == 1) { // login google já existente, valida e redireciona


                    localStorage.removeItem('cockpit_carregado');
 
                     Swal.fire({
                     icon: 'success',
                     title: 'Parabéns!',
                     html: data.retorno,
                     showConfirmButton: false,
                     imageUrl: '/tema/src/media/svg/illustrations/progress.svg',
                     imageWidth: 400,
                     timer: 3000,
   timerProgressBar: true,
   didOpen: () => {
     Swal.showLoading()
     const b = Swal.getHtmlContainer().querySelector('b')
     timerInterval = setInterval(() => {
      b.textContent = Math.ceil(Swal.getTimerLeft() / 1000);
     }, 3000)
   },
   willClose: () => {
     clearInterval(timerInterval)
   }                 
                     });
 
 
                   
 setTimeout(() => {
           // redireciona
 
           if (data.nivel != "operador") {
                                     window.location.href = "./views/dashboard.php"; //"../dashboard/home.php";
 
                                     console.log("entrou");
                                 }
                                
                                 if (data.nivel == "operador") {
 
 
                                     console.log("entrou operador");
                                     window.location.href = "./app/";
 
                                 }
 }, 3000);
                  
 
 
                 }
              
                 if (data.codigo == 0) { // usuário inativo no sistema
 
                     Swal.fire({
                     icon: 'error',
                     title: 'Acesso não Autorizado',
                     html: data.retorno ,
                     buttonsStyling: false,
         confirmButtonText: "OK, farei isso!",
         customClass: {
             confirmButton: "btn btn-primary"
         }                  
                     });
 
 
                     }
 
              
 
             },
             error: function (data) {
 
                 Swal.fire({
                     icon: 'error',
                     title: 'Falha na Consulta com o Banco de Dados',
                     html: data.retorno,
                     buttonsStyling: false,
         confirmButtonText: "OK, farei isso!",
         customClass: {
             confirmButton: "btn btn-danger"
         }                           
                     });
 
                 console.log('Falha no Processamento dos Dados.');
 
 
              
 
 
             }
 
         });
   }