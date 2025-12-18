document.addEventListener("DOMContentLoaded", function () {
  // Animate Activity Bars
  const bars = document.querySelectorAll(".activity-bar");
  bars.forEach((bar) => {
    const height = bar.getAttribute("data-height");
    setTimeout(() => {
      bar.style.height = height;
    }, 300);
  });

  // Animate Circular Metrics
  const circles = document.querySelectorAll(".metric-fill");
  circles.forEach((circle) => {
    const percent = circle.getAttribute("data-percent");
    const circumference = 251.2;
    const offset = circumference - (percent / 100) * circumference;

    setTimeout(() => {
      circle.style.strokeDashoffset = offset;
    }, 500);
  });

  // Greeting
  const greetingElement = document.getElementById("userGreeting");
  if (greetingElement) {
    const hour = new Date().getHours();
    let greeting = "Welcome back";
    if (hour < 12) greeting = "Good Morning";
    else if (hour < 18) greeting = "Good Afternoon";
    else greeting = "Good Evening";

    const namePart = greetingElement.textContent.split(",")[1] || "";
    greetingElement.textContent = `${greeting},${namePart}`;
  }
});

function toggleSidebar() {
  const sidebar = document.getElementById("sidebar");
  const overlay = document.getElementById("sidebarOverlay");

  if (sidebar.classList.contains("-translate-x-full")) {
    sidebar.classList.remove("-translate-x-full");
    overlay.classList.remove("hidden");
  } else {
    sidebar.classList.add("-translate-x-full");
    overlay.classList.add("hidden");
  }
}
