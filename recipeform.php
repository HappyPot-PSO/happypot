<!-- Post a Recipe Form -->
<div class="recipeform">
  <h2 class="underline2">Post a new recipe!</h2><br>
  <div id="error"></div>
  <form id="recipeform" method="POST" action="recipe_upload.php" enctype="multipart/form-data">
    <p>Recipe name:<input id="name" class="recipebox" type="text" name="name"></p>
    <p>Recipe image:<input id="img" type="file" name="img" accept="image/*"></p>
    <p>Prep time:<select id="time" name="time" size="1">
      <option selected value="epilogi">Pick Time</option>
      <option value="5">5 min</option>
      <option value="10">10 min</option>
      <option value="15">15 min</option>
      <option value="30">30 min</option>
      <option value="45">45 min</option>
      <option value="60">60 min</option>
      <option value="90">90 min</option>
      <option value="120">120 min</option>
    </select></p><br><br>
    <p>Ingredients:</p>
    <textarea id="ingr" class="txtarea" name="ingredients" cols="100" rows="5"></textarea>
    <p id="ingr-counter"></p><br><br>
    <p>Instructions:</p>
    <textarea id="instr" class="txtarea" name="instructions" cols="100" rows="10"></textarea>
    <p id="instr-counter"></p><br><br>
    <!-- Add the user ID as a hidden input field -->
    <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
    <input id="recipost" class="postbtn btnhov" type="submit" name="post" value="Post Recipe">
    <button class="cancelbtn btnhovcl" type="button" onClick="location.href='index.php'" name="cancel">Cancel</button>
  </form>
</div>

<script>
  const ingrTextarea = document.getElementById("ingr");
  const instrTextarea = document.getElementById("instr");
  const ingrCharCount = document.getElementById("ingr-counter");
  const instrCharCount = document.getElementById("instr-counter");

  ingrTextarea.addEventListener("input", function() {
    const maxLength = 500;
    let currentLength = ingrTextarea.value.length;

    if (currentLength > maxLength) {
      ingrTextarea.value = ingrTextarea.value.slice(0, maxLength);
      currentLength = maxLength;
    }

    ingrCharCount.textContent = currentLength + "/" + maxLength;
  });

  instrTextarea.addEventListener("input", function() {
    const maxLength = 1000;
    let currentLength = instrTextarea.value.length;

    if (currentLength > maxLength) {
      instrTextarea.value = instrTextarea.value.slice(0, maxLength);
      currentLength = maxLength;
    }

    instrCharCount.textContent = currentLength + "/" + maxLength;
  });
</script>