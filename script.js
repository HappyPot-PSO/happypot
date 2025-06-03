// Register form validation
document.addEventListener('DOMContentLoaded', function() {
  const registerForm = document.getElementById('registerform');

  if (registerForm) {
    const registerButton = document.getElementById('registerbtn');
    const errorElement = document.getElementById('error');

    registerButton.addEventListener('click', (e) => {
      e.preventDefault();

      let messages = [];
      const fname = document.getElementById('fname');
      const lname = document.getElementById('lname');
      const remail = document.getElementById('remail');
      const rpassword = document.getElementById('rpassword');
      const repassword = document.getElementById('repassword');

     // Check if fname field is empty
      if (!fname.value) {
        messages.push('First name is required');
      } 
    
      // Check if fname length
      if (fname.value.length > 12) {
        messages.push('First name can\'t be longer than 12 characters');
      }

      if (!lname.value) {
        messages.push('Last name is required');
      }

      if (lname.value.length > 12) {
        messages.push('Last name can\'t be longer than 12 characters');
      }
           
      if (!remail.value) {
        messages.push('E-mail is required');
      } else {
        // Check if e-mail is valid
        let re = /^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/;
        if (!re.test(remail.value)) {
          messages.push('E-mail is not valid');
        }
      }

      if (!rpassword.value) {
        messages.push('Password is required');
      }

      // Check password length
      if (rpassword.value.length < 8) {
        messages.push('Password must contain at least 8 characters');
      }

      // Check if passwords match
      if (rpassword.value !== repassword.value) {
        messages.push("Passwords don't match");
      }
      
      // Display error message, else submit form
      if (messages.length > 0) {
        errorElement.innerText = messages.join(', ');
      } else {
        registerForm.submit();
      }
    });
  }
});

document.addEventListener('DOMContentLoaded', function() {
  const recipeForm = document.getElementById('recipeform');
  const errorElement = document.getElementById('error');

  if (recipeForm) {
    const postButton = document.getElementById('recipost');

    postButton.addEventListener('click', (e) => {
      e.preventDefault();

      let messages = [];
      const name = document.getElementById('name');
      const img = document.getElementById('img');
      const time = document.getElementById('time');
      const ingr = document.getElementById('ingr');
      const instr = document.getElementById('instr');

      // Check for empty fields
      if (!name.value) {
        messages.push('Recipe name is required');
      }

      if (name.value.length > 20) {
        messages.push('Recipe name can\'t be longer than 20 characters');
      }

      if (!img.value) {
        messages.push('Image is required');
      }

      if (!time.value || time.value === 'epilogi') {
        messages.push('Prep time is required');
      }

      if (!ingr.value) {
        messages.push('Ingredients are required');
      }
      
      if (ingr.value.length > 500) {
        messages.push('Ingredients can\'t be longer than 500 characters');
      }

      if (!instr.value) {
        messages.push('Instructions are required');
      }

      if (instr.value.length > 1000) {
        messages.push('Instructions can\'t be longer than 1000 characters');
      }

      // Display error message, else submit form
      if (messages.length > 0) {
        errorElement.innerText = messages.join(', ');
      } else {
        recipeForm.submit();
      }
    });
  }
});