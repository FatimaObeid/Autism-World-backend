function ValidateRegisterform(){
    const name=document.getElementById('name').value;
    const email=document.getElementById('email').value;
    const password=document.getElementById('password').value;
    const role=document.getElementById('role').value;
    if(!name||!email||!password||!role){
        alert("Please fill in all fields");
        return false;
    }
    if(password.length<6){
        alert('Password must be at least 6 characters');
        return false;
    }
    alert(`Registration successful!\nName:${name}\nEmail:${email}\nRole:${role}`);
return true;
}