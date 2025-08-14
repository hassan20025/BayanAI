document.addEventListener("DOMContentLoaded", () => {
  // --- Navigation and Mobile Menu ---
    const infoModal = document.getElementById("info-modal");
  if (infoModal) {
    infoModal.classList.add("hidden"); // Force hidden
    document.body.style.overflow = ""; // Enable scrolling
  }
  const mobileMenuButton = document.getElementById("mobile-menu-button");
  const mobileMenu = document.getElementById("mobile-menu");
  const menuIcon = document.getElementById("menu-icon");
  const closeIcon = document.getElementById("close-icon");
  const navLinks = document.querySelectorAll(".nav-link, .mobile-nav-link");

  if (mobileMenuButton && mobileMenu && menuIcon && closeIcon) {
    mobileMenuButton.addEventListener("click", () => {
      mobileMenu.classList.toggle("hidden");
      menuIcon.classList.toggle("hidden");
      closeIcon.classList.toggle("hidden");
    });
  }

  if (navLinks.length > 0 && mobileMenu && menuIcon && closeIcon) {
    navLinks.forEach((link) => {
      link.addEventListener("click", (e) => {
        e.preventDefault();
        const targetId = link.getAttribute("href").substring(1);
        const targetElement = document.getElementById(targetId);

        if (targetElement) {
          targetElement.scrollIntoView({ behavior: "smooth" });
          if (!mobileMenu.classList.contains("hidden")) {
            mobileMenu.classList.add("hidden");
            menuIcon.classList.remove("hidden");
            closeIcon.classList.add("hidden");
          }
        }
      });
    });
  }

  // --- Hero Section "Get Started" Button ---
  const heroGetStartedButton = document.getElementById("hero-get-started-button");
  if (heroGetStartedButton) {
    heroGetStartedButton.addEventListener("click", (e) => {
      e.preventDefault();
      const targetId = heroGetStartedButton.getAttribute("href").substring(1);
      const targetElement = document.getElementById(targetId);
      if (targetElement) {
        targetElement.scrollIntoView({ behavior: "smooth" });
      }
    });
  }

  // --- Info Modal ---
  const learnMoreButton = document.getElementById("learn-more-button");

  const modalCloseButton = document.getElementById("modal-close-button");

  if (learnMoreButton && infoModal && modalCloseButton) {
    learnMoreButton.addEventListener("click", () => {
      infoModal.classList.remove("hidden");
      document.body.style.overflow = "hidden";
    });

    modalCloseButton.addEventListener("click", () => {
      infoModal.classList.add("hidden");
      document.body.style.overflow = "";
    });

    infoModal.addEventListener("click", (e) => {
      if (e.target === infoModal) {
        infoModal.classList.add("hidden");
        document.body.style.overflow = "";
      }
    });
  }

  // --- Contact Form ---
// --- Contact Form ---
const contactForm = document.getElementById("contact-form");
const formStatus = document.getElementById("form-status");

if (contactForm && formStatus) {
  contactForm.addEventListener("submit", (e) => {
    e.preventDefault();
    formStatus.textContent = "Sending...";
    formStatus.classList.remove("text-green-400", "text-red-400");
    formStatus.classList.add("text-gray-400");

    const formData = new FormData(contactForm);

    fetch("api/landingPage/landingPage.php", {
      method: "POST",
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.status === "success") {
        formStatus.textContent = data.message;
        formStatus.classList.remove("text-gray-400");
        formStatus.classList.add("text-green-400");
        contactForm.reset();
      } else {
        formStatus.textContent = data.message || "Something went wrong.";
        formStatus.classList.remove("text-gray-400");
        formStatus.classList.add("text-red-400");
      }
    })
    .catch(err => {
      console.error(err);
      formStatus.textContent = "Failed to send message.";
      formStatus.classList.remove("text-gray-400");
      formStatus.classList.add("text-red-400");
    });
  });
}


  // --- Pricing Section Carousel ---
  const pricingCards = document.querySelectorAll(".pricing-card");
  let selectedPlanIndex = 1;
  const cardWidth = 350;
  const cardGap = 48;

  const updatePricingCards = () => {
    const numCards = pricingCards.length;

    pricingCards.forEach((card, index) => {
      let relativeOrder = 2;
      if (index === selectedPlanIndex) relativeOrder = 0;
      else if ((selectedPlanIndex + 1) % numCards === index) relativeOrder = 1;

      let leftPosition;
      switch (relativeOrder) {
        case 0:
          leftPosition = `calc(50% - ${cardWidth / 2}px)`;
          break;
        case 1:
          leftPosition = `calc(50% + ${cardWidth / 2 + cardGap}px)`;
          break;
        case 2:
          leftPosition = `calc(50% - ${cardWidth / 2 + cardWidth + cardGap}px)`;
          break;
        default:
          leftPosition = `calc(50% - ${cardWidth / 2}px)`;
      }

      const transformScale = relativeOrder === 0 ? 1.15 : 0.85;
      const opacity = relativeOrder === 0 ? 1 : 0.6;
      const zIndex = relativeOrder === 0 ? 20 : 10;

      card.style.left = leftPosition;
      card.style.transform = `scale(${transformScale})`;
      card.style.opacity = opacity;
      card.style.zIndex = zIndex;
    });
  };

  if (pricingCards.length > 0) {
    pricingCards.forEach((card, index) => {
      card.addEventListener("click", () => {
        selectedPlanIndex = index;
        updatePricingCards();
      });
    });
    updatePricingCards();
  }

  // --- Particle Background (Canvas) ---
  const canvas = document.getElementById("particle-canvas");
  if (canvas) {
    const ctx = canvas.getContext("2d");
    if (!ctx) return;

    const particles = [];
    const particleCount = 50;

    const resizeCanvas = () => {
      canvas.width = window.innerWidth;
      canvas.height = window.innerHeight;
    };

    const createParticle = () => ({
      x: Math.random() * canvas.width,
      y: Math.random() * canvas.height,
      vx: (Math.random() - 0.5) * 0.5,
      vy: (Math.random() - 0.5) * 0.5,
      size: Math.random() * 2 + 1,
      opacity: Math.random() * 0.5 + 0.2,
      color: Math.random() > 0.5 ? "#5aa2fa" : "#ffffff",
    });

    const initParticles = () => {
      particles.length = 0;
      for (let i = 0; i < particleCount; i++) {
        particles.push(createParticle());
      }
    };

    const updateParticles = () => {
      particles.forEach((p) => {
        p.x += p.vx;
        p.y += p.vy;
        if (p.x < 0 || p.x > canvas.width) p.vx *= -1;
        if (p.y < 0 || p.y > canvas.height) p.vy *= -1;
        p.opacity = 0.2 + Math.sin(Date.now() * 0.001 + p.x * 0.01) * 0.3;
      });
    };

    const drawParticles = () => {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      particles.forEach((p) => {
        ctx.save();
        ctx.globalAlpha = p.opacity;
        ctx.fillStyle = p.color;
        ctx.beginPath();
        ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
        ctx.fill();
        ctx.restore();
      });

      particles.forEach((p1, i) => {
        particles.slice(i + 1).forEach((p2) => {
          const dx = p1.x - p2.x;
          const dy = p1.y - p2.y;
          const dist = Math.sqrt(dx * dx + dy * dy);
          if (dist < 100) {
            ctx.save();
            ctx.globalAlpha = ((100 - dist) / 100) * 0.2;
            ctx.strokeStyle = "#5aa2fa";
            ctx.lineWidth = 1;
            ctx.beginPath();
            ctx.moveTo(p1.x, p1.y);
            ctx.lineTo(p2.x, p2.y);
            ctx.stroke();
            ctx.restore();
          }
        });
      });
    };

    const animate = () => {
      updateParticles();
      drawParticles();
      requestAnimationFrame(animate);
    };

    resizeCanvas();
    initParticles();
    animate();
    window.addEventListener("resize", () => {
      resizeCanvas();
      initParticles();
    });
  }
});
