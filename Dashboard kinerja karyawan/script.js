document.getElementById("loginForm").addEventListener("submit", function(e) {
  e.preventDefault();

  const username = document.getElementById("username").value;
  const password = document.getElementById("password").value;

  // Validasi sederhana (sementara)
  if (username === "Abdi" && password === "8125") {
    window.location.href = "dashboard.html";
  } else {
    alert("Username atau password salah!");
  }
});
