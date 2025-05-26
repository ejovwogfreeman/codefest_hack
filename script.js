const hamburger = document.getElementById("hamburger");
const menu = document.getElementById("menu");
const nav = document.querySelector("nav");

window.addEventListener("scroll", () => {
  if (window.scrollY > 200) {
    menu.style.background = "rgba(0, 0, 0, 0.95)";
    nav.style.background = "rgba(0, 0, 0, 0.95)";
  } else {
    menu.style.background = "transparent";
    nav.style.background = "transparent";
  }
});

function toggleMenu() {
  const isVisible = menu.classList.toggle("show");
  hamburger.classList.toggle("active");

  // Update aria-expanded and aria-hidden for accessibility
  hamburger.setAttribute("aria-expanded", isVisible);
  menu.setAttribute("aria-hidden", !isVisible);
}

hamburger.addEventListener("click", toggleMenu);

// Optional: Allow keyboard toggle via Enter/Space on hamburger
hamburger.addEventListener("keydown", (e) => {
  if (e.key === "Enter" || e.key === " ") {
    e.preventDefault();
    toggleMenu();
  }
});

// Countdown Timer
const targetDate = new Date("Dec 15, 2025 16:30:00").getTime();

function updateCountdown() {
  const now = new Date().getTime();
  const diff = targetDate - now;

  if (diff <= 0) {
    document.getElementById("days").textContent = "00";
    document.getElementById("hours").textContent = "00";
    document.getElementById("minutes").textContent = "00";
    document.getElementById("seconds").textContent = "00";
    clearInterval(countdownInterval);
    return;
  }

  const days = Math.floor(diff / (1000 * 60 * 60 * 24));
  const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
  const seconds = Math.floor((diff % (1000 * 60)) / 1000);

  document.getElementById("days").textContent = days
    .toString()
    .padStart(2, "0");
  document.getElementById("hours").textContent = hours
    .toString()
    .padStart(2, "0");
  document.getElementById("minutes").textContent = minutes
    .toString()
    .padStart(2, "0");
  document.getElementById("seconds").textContent = seconds
    .toString()
    .padStart(2, "0");
}

const countdownInterval = setInterval(updateCountdown, 1000);
updateCountdown();

// Star generation for background
function createStars() {
  const container = document.getElementById("space-header");
  const starCount = 100;

  for (let i = 0; i < starCount; i++) {
    const star = document.createElement("div");
    star.classList.add("star");

    const size = Math.random() * 2 + 1; // 1px to 3px
    star.style.width = `${size}px`;
    star.style.height = `${size}px`;

    // random position within container
    star.style.top = `${Math.random() * 100}%`;
    star.style.left = `${Math.random() * 100}%`;

    // random animation delay to twinkle at different times
    star.style.animationDelay = `${Math.random() * 3}s`;

    container.appendChild(star);
  }
}
createStars();

document.addEventListener("DOMContentLoaded", () => {
  const images = document.querySelectorAll(".floating-images img");

  images.forEach((img) => {
    const top = Math.random() * 90;
    const left = Math.random() * 90;
    const size = 40 + Math.random() * 60;
    const delay = Math.random() * 5;

    img.style.top = `${top}%`;
    img.style.left = `${left}%`;
    img.style.width = `${size}px`;
    img.style.animationDelay = `${delay}s`;
  });
});

document.addEventListener("DOMContentLoaded", () => {
  const items = document.querySelectorAll(".accordion-item");

  // Set first item open by default
  if (items.length > 0) {
    const firstHeader = items[0].querySelector(".accordion-header");
    const firstBody = items[0].querySelector(".accordion-body");
    firstHeader.classList.add("active");
    firstBody.style.maxHeight = firstBody.scrollHeight + "px";
  }

  items.forEach((item) => {
    const header = item.querySelector(".accordion-header");
    const body = item.querySelector(".accordion-body");

    header.addEventListener("click", () => {
      const currentlyOpen = document.querySelector(".accordion-header.active");

      if (currentlyOpen && currentlyOpen !== header) {
        currentlyOpen.classList.remove("active");
        currentlyOpen.nextElementSibling.style.maxHeight = null;
      }

      header.classList.toggle("active");
      if (header.classList.contains("active")) {
        body.style.maxHeight = body.scrollHeight + "px";
      } else {
        body.style.maxHeight = null;
      }
    });
  });
});
