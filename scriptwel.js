const sign= document.getElementById("sign");
const login= document.getElementById("login");
const tosign= document.getElementById("tosignup");
const tologin= document.getElementById("tologin");

tologin.addEventListener('click', function(e){
  e.preventDefault();
  sign.classList.add('hidden');
  login.classList.remove('hidden');
});

tosign.addEventListener('click', function(e){
  e.preventDefault();
  login.classList.add('hidden');
  sign.classList.remove('hidden');
});

tosign.addEventListener('submit', function(e){
  e.preventDefault();
});