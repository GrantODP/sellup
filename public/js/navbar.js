

document.addEventListener("DOMContentLoaded", async function () {

  const userLink = document.getElementById('user-link');
  const user = JSON.parse(localStorage.getItem('user'));
  if (user) {
    const html = `<img src= "/c2c-commerce-site/${user.profile_pic ?? "profile.png"}" alt = "Profile Picture"
    class=""rounded - circle me - 3" width="32" height="32">
      <span id = "user-link-name" class="align-middle" > ${user.name ?? "User"}</span >`

    userLink.innerHTML = html;

    console.log(user.profile_pic);
  }
  else {
    const html = `<img src = "/c2c-commerce-site/media/profile.png" alt = "Profile Picture"
    class=""rounded - circle me - 3" width="32" height="32">
      <span id = user-link-name" class="align-middle" > ${"User"}</span > `

    userLink.innerHTML = html;
  }
});
