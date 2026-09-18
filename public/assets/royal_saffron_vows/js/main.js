const defaultWeddingData = {
  sectionVisibility: {
    hero: true,
    couple: true,
    story: true,
    family: true,
    ceremonies: true,
    gallery: true,
    countdown: true,
    venue: true,
    rsvp: true,
    footer: true
  },
  bride: {
    name: "Priya Sharma",
    initial: "P",
    photo: "https://images.unsplash.com/photo-1583391733981-849840c5d628?auto=format&fit=crop&w=900&q=80",
    photoAlt: "Bride portrait placeholder",
    bio: "A graceful soul with a love for classical music, family traditions, and warm celebrations."
  },
  groom: {
    name: "Rahul Mehta",
    initial: "R",
    photo: "https://images.unsplash.com/photo-1610173827043-62b52d7c2300?auto=format&fit=crop&w=900&q=80",
    photoAlt: "Groom portrait placeholder",
    bio: "A thoughtful heart who finds joy in travel, food, and gathering everyone he loves in one place."
  },
  wedding: {
    templateName: "InviteCraft",
    invitationLabel: "Wedding Invitation",
    openingMessage: "invite you to celebrate their wedding",
    date: "2027-02-14T19:00:00+05:30",
    displayDate: "14 February 2027",
    message: "Together with their families, they invite you to an evening of blessings, rituals, music, and love.",
    storyTitle: "A story written in little moments",
    story: "From a first conversation over chai to a promise made under festive lights, this celebration honors every step that brought them here.",
    heroImage: "https://images.unsplash.com/photo-1587271636175-90d58cdad458?auto=format&fit=crop&w=1800&q=80",
    storyImage: "https://images.unsplash.com/photo-1606800052052-a08af7148866?auto=format&fit=crop&w=1800&q=80",
    footerText: "Made with love for their wedding celebration",
    backgroundMusic: "assets/audio/traditional-invitation-wedding-invitation-rajasthani-einvite.mp3"
  },
  storyMilestones: [
    { title: "First Hello", date: "Spring 2023", description: "A warm conversation became the beginning of a beautiful bond." },
    { title: "Families Met", date: "Winter 2024", description: "Blessings, laughter, and shared meals brought everyone closer." },
    { title: "The Promise", date: "Autumn 2026", description: "They chose forever, surrounded by the people they love most." }
  ],
  ceremonies: [
    { title: "Haldi", dateTime: "12 February 2027, 10:00 AM", venue: "Courtyard Lawn", description: "A sunlit morning of turmeric, laughter, and family blessings.", image: "https://images.unsplash.com/photo-1596778151856-7d94cfef393d?auto=format&fit=crop&w=1000&q=80", imageAlt: "Haldi ceremony placeholder" },
    { title: "Mehendi", dateTime: "12 February 2027, 5:00 PM", venue: "Garden Pavilion", description: "Intricate henna, folk songs, and an evening dressed in color.", image: "https://images.unsplash.com/photo-1583939003579-730e3918a45a?auto=format&fit=crop&w=1000&q=80", imageAlt: "Mehendi ceremony placeholder" },
    { title: "Sangeet", dateTime: "13 February 2027, 7:30 PM", venue: "Royal Ballroom", description: "Music, dance, and performances from both families.", image: "https://images.unsplash.com/photo-1519225421980-715cb0215aed?auto=format&fit=crop&w=1000&q=80", imageAlt: "Sangeet celebration placeholder" },
    { title: "Wedding", dateTime: "14 February 2027, 7:00 PM", venue: "Mandap Gardens", description: "Sacred vows, floral decor, and blessings beneath the mandap.", image: "https://images.unsplash.com/photo-1623039405147-547794f92e9e?auto=format&fit=crop&w=1000&q=80", imageAlt: "Wedding mandap placeholder" },
    { title: "Reception", dateTime: "15 February 2027, 8:00 PM", venue: "Grand Banquet Hall", description: "A formal dinner reception to celebrate the newlyweds.", image: "https://images.unsplash.com/photo-1464366400600-7168b8af9bc3?auto=format&fit=crop&w=1000&q=80", imageAlt: "Reception decor placeholder" }
  ],
  family: {
    brideSide: {
      title: "Bride's Family",
      members: [
        { name: "Mr. Anil Sharma", relation: "Father of the Bride" },
        { name: "Mrs. Kavita Sharma", relation: "Mother of the Bride" },
        { name: "Aarav Sharma", relation: "Brother of the Bride" }
      ]
    },
    groomSide: {
      title: "Groom's Family",
      members: [
        { name: "Mr. Suresh Mehta", relation: "Father of the Groom" },
        { name: "Mrs. Neeta Mehta", relation: "Mother of the Groom" },
        { name: "Isha Mehta", relation: "Sister of the Groom" }
      ]
    }
  },
  gallery: [
    { src: "https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=900&q=80", alt: "Couple celebration placeholder" },
    { src: "https://images.unsplash.com/photo-1523438885200-e635ba2c371e?auto=format&fit=crop&w=900&q=80", alt: "Wedding decor placeholder" },
    { src: "https://images.unsplash.com/photo-1505944357431-27579db47558?auto=format&fit=crop&w=900&q=80", alt: "Wedding table styling placeholder" },
    { src: "https://images.unsplash.com/photo-1511285560929-80b456fea0bc?auto=format&fit=crop&w=900&q=80", alt: "Wedding rings placeholder" },
    { src: "https://images.unsplash.com/photo-1465495976277-4387d4b0e4a6?auto=format&fit=crop&w=900&q=80", alt: "Wedding lights placeholder" },
    { src: "https://images.unsplash.com/photo-1529634806980-85c3dd6d34ac?auto=format&fit=crop&w=900&q=80", alt: "Wedding flowers placeholder" }
  ],
  venue: {
    name: "The Palace Greens",
    city: "Jaipur, Rajasthan",
    address: "Amer Road, Jaipur, Rajasthan 302002",
    mapUrl: "https://www.google.com/maps",
    embedMapUrl: "https://www.google.com/maps?q=Jaipur%20Rajasthan&output=embed"
  },
  contact: {
    name: "Event Desk",
    phone: "+91 98765 43210",
    phoneHref: "tel:+919876543210"
  }
};

function mergeWeddingData(defaults, overrides) {
  if (!overrides || typeof overrides !== "object") return defaults;

  const merged = { ...defaults };
  Object.entries(overrides).forEach(([key, value]) => {
    if (Array.isArray(value)) {
      merged[key] = value;
      return;
    }

    if (value && typeof value === "object" && defaults[key] && typeof defaults[key] === "object" && !Array.isArray(defaults[key])) {
      merged[key] = mergeWeddingData(defaults[key], value);
      return;
    }

    if (value !== undefined && value !== null) merged[key] = value;
  });

  return merged;
}

const weddingData = mergeWeddingData(defaultWeddingData, window.InviteCraftWeddingData);

const selectors = {
  section: "[data-section]",
  bind: "[data-bind]",
  bindSrc: "[data-bind-src]",
  bindHref: "[data-bind-href]",
  dynamicBg: "[data-dynamic-bg]",
  familyList: "[data-family-list]",
  ceremonyList: "[data-ceremony-list]",
  galleryList: "[data-gallery-list]",
  rsvpForm: "[data-rsvp-form]",
  rsvpFeedback: "[data-rsvp-feedback]",
  countdown: "[data-countdown]",
  countdownContent: "[data-countdown-content]",
  countdownScratch: "[data-countdown-scratch]",
  countdownScratchWrapper: "[data-countdown-scratch-wrapper]",
  countdownBurstLayer: "[data-countdown-burst-layer]"
};

const state = {
  reducedMotion: false,
  hasOpenedInvitation: false,
  petalTweens: [],
  petalPaused: false,
  galleryIndex: 0,
  threeAnimationId: null,
  threeRenderer: null,
  threeScene: null,
  threeCamera: null,
  threeGroup: null,
  countdownIntervalId: null,
  countdownStarted: false,
  scratchInitialized: false,
  scratchCompleted: false
};

document.addEventListener("DOMContentLoaded", () => {
  resetPageToTop();
  initReducedMotion();
  applySectionVisibility(weddingData.sectionVisibility);
  bindScalarContent(weddingData);
  renderFamilies(weddingData.family);
  renderCeremonies(weddingData.ceremonies);
  renderStoryTimeline(weddingData.storyMilestones);
  renderGallery(weddingData.gallery);
  initMobileNavigation();
  initBackgroundMusic();
  initOpeningScreen();
  initCountdownScratch(weddingData.wedding.date);
  initRSVPValidation();
  initGallery();
  initParticles();
  initThreeBackground();
  initScrollAnimations();
  initStoryTimeline();
  initPageVisibilityPerformance();
});

function initReducedMotion() {
  state.reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  if (state.reducedMotion) document.documentElement.classList.add("reduced-motion");
}

function getValue(path, source) {
  return path.split(".").reduce((value, key) => (value ? value[key] : undefined), source);
}

function applySectionVisibility(visibility = {}) {
  document.querySelectorAll(selectors.section).forEach((section) => {
    const key = section.dataset.section;
    if (visibility[key] === false) {
      section.hidden = true;
      document.querySelectorAll(`[data-section-link="${key}"], a[href="#${key}"]`).forEach((link) => {
        const item = link.closest("li") || link;
        item.hidden = true;
      });
    }
  });
}

function bindScalarContent(data) {
  document.querySelectorAll(selectors.bind).forEach((node) => {
    const value = getValue(node.dataset.bind, data);
    if (value !== undefined && value !== null) node.textContent = value;
  });
  document.querySelectorAll(selectors.bindSrc).forEach((node) => {
    const value = getValue(node.dataset.bindSrc, data);
    const altValue = node.dataset.bindAlt ? getValue(node.dataset.bindAlt, data) : "";
    if (value) node.setAttribute("src", value);
    if (altValue) node.setAttribute("alt", altValue);
  });
  document.querySelectorAll(selectors.bindHref).forEach((node) => {
    const value = getValue(node.dataset.bindHref, data);
    if (value) node.setAttribute("href", value);
  });
  document.querySelectorAll(selectors.dynamicBg).forEach((node) => {
    const value = getValue(node.dataset.dynamicBg, data);
    if (value) node.style.backgroundImage = `url("${value}")`;
  });
  document.documentElement.style.setProperty("--story-image", `url("${data.wedding.storyImage}")`);
}

function renderFamilies(familyGroups) {
  const target = document.querySelector(selectors.familyList);
  if (!target || !familyGroups) return;
  target.innerHTML = "";
  Object.values(familyGroups).forEach((group) => {
    const article = document.createElement("article");
    article.className = "family-group";
    article.setAttribute("data-family-group", "");
    const title = document.createElement("h3");
    title.textContent = group.title;
    const list = document.createElement("ul");
    group.members.forEach((member) => {
      const item = document.createElement("li");
      item.setAttribute("data-family-member", "");
      item.innerHTML = '<span class="family-name"></span><span class="family-relation"></span>';
      item.querySelector(".family-name").textContent = member.name;
      item.querySelector(".family-relation").textContent = member.relation;
      list.appendChild(item);
    });
    article.append(title, list);
    target.appendChild(article);
  });
}

function renderCeremonies(ceremonies = []) {
  const target = document.querySelector(selectors.ceremonyList);
  if (!target) return;
  const accents = ["#f3c533", "#2f8b57", "#bf8d2c", "#9f2443", "#e8c874"];
  target.innerHTML = "";
  ceremonies.forEach((ceremony, index) => {
    const item = document.createElement("article");
    item.className = "ceremony-item";
    item.setAttribute("data-ceremony-card", "");
    item.style.setProperty("--event-accent", accents[index % accents.length]);
    item.innerHTML = `
      <img class="ceremony-image" loading="lazy" decoding="async">
      <div class="ceremony-copy">
        <p class="eyebrow ceremony-time"></p>
        <h3></h3>
        <p class="ceremony-venue"></p>
        <p class="ceremony-description"></p>
      </div>
    `;
    const image = item.querySelector("img");
    image.src = ceremony.image;
    image.alt = ceremony.imageAlt || `${ceremony.title} ceremony`;
    item.querySelector(".ceremony-time").textContent = ceremony.dateTime;
    item.querySelector("h3").textContent = ceremony.title;
    item.querySelector(".ceremony-venue").textContent = ceremony.venue;
    item.querySelector(".ceremony-description").textContent = ceremony.description;
    target.appendChild(item);
  });
}

function renderStoryTimeline(milestones = []) {
  const target = document.querySelector("[data-story-milestones]");
  if (!target) return;
  target.innerHTML = "";
  milestones.forEach((milestone) => {
    const item = document.createElement("article");
    item.className = "story-milestone";
    item.innerHTML = '<span class="story-dot" aria-hidden="true"></span><time></time><h3></h3><p></p>';
    item.querySelector("time").textContent = milestone.date;
    item.querySelector("h3").textContent = milestone.title;
    item.querySelector("p").textContent = milestone.description;
    target.appendChild(item);
  });
}

function renderGallery(images = []) {
  const target = document.querySelector(selectors.galleryList);
  if (!target) return;
  target.innerHTML = "";
  images.forEach((image, index) => {
    const button = document.createElement("button");
    button.className = "gallery-item";
    button.type = "button";
    button.setAttribute("data-gallery-image", "");
    button.setAttribute("data-gallery-index", String(index));
    button.setAttribute("aria-label", `Open gallery image ${index + 1}`);
    const img = document.createElement("img");
    img.src = image.src;
    img.alt = image.alt || "Wedding gallery image";
    img.loading = "lazy";
    img.decoding = "async";
    button.appendChild(img);
    target.appendChild(button);
  });
}

function initOpeningScreen() {
  const screen = document.querySelector("[data-opening-screen]");
  const button = document.querySelector("[data-open-invitation]");
  if (!screen || !button) {
    resetPageToTop();
    startInvitationExperience();
    return;
  }
  resetPageToTop();
  document.body.classList.add("invitation-locked");
  if (!state.reducedMotion && window.gsap) {
    window.gsap.from(".opening-card > *", { y: 22, opacity: 0, duration: 0.9, stagger: 0.12, ease: "power2.out" });
  }
  button.addEventListener("click", async () => {
    state.hasOpenedInvitation = true;
    resetPageToTop();
    playWeddingMusic();
    if (!state.reducedMotion && window.gsap) {
      window.gsap.timeline({
        onComplete: () => {
          screen.hidden = true;
          document.body.classList.remove("invitation-locked");
          resetPageToTop();
          startInvitationExperience();
        }
      })
        .to(".opening-card", { y: -24, opacity: 0, duration: 0.45, ease: "power2.in" })
        .to(".opening-door-left", { xPercent: -100, duration: 0.8, ease: "power3.inOut" }, 0.12)
        .to(".opening-door-right", { xPercent: 100, duration: 0.8, ease: "power3.inOut" }, 0.12);
    } else {
      screen.hidden = true;
      document.body.classList.remove("invitation-locked");
      resetPageToTop();
      startInvitationExperience();
    }
  }, { once: true });
}

function resetPageToTop() {
  if ("scrollRestoration" in window.history) {
    window.history.scrollRestoration = "manual";
  }
  window.scrollTo({ top: 0, left: 0, behavior: "auto" });
}

function startInvitationExperience() {
  initHeroAnimation();
  initFlowerPetals();
}

function initBackgroundMusic() {
  const audio = document.querySelector("#weddingMusic");
  const toggle = document.querySelector("[data-floating-music-toggle]");
  if (!audio || !toggle) return;
  if (!weddingData.wedding.backgroundMusic) {
    audio.remove();
    toggle.hidden = true;
    return;
  }
  audio.loop = true;
  const source = audio.querySelector("source");
  if (source && weddingData.wedding.backgroundMusic) {
    source.src = weddingData.wedding.backgroundMusic;
    audio.load();
  }
  toggle.hidden = false;
  toggle.addEventListener("click", async () => {
    if (audio.paused) await playWeddingMusic();
    else {
      audio.pause();
      updateMusicButton(false);
    }
  });
  audio.addEventListener("pause", () => updateMusicButton(false));
  audio.addEventListener("play", () => updateMusicButton(true));
  audio.addEventListener("error", () => {
    updateMusicButton(false);
    const label = toggle.querySelector("[data-floating-music-label]");
    if (label) label.textContent = "Music Unavailable";
  });
}

async function playWeddingMusic() {
  const audio = document.querySelector("#weddingMusic");
  if (!audio) return false;
  try {
    await audio.play();
    updateMusicButton(true);
    return true;
  } catch (error) {
    updateMusicButton(false);
    return false;
  }
}

function updateMusicButton(isPlaying) {
  const toggle = document.querySelector("[data-floating-music-toggle]");
  const label = document.querySelector("[data-floating-music-label]");
  if (!toggle || !label) return;
  toggle.classList.toggle("is-playing", isPlaying);
  toggle.setAttribute("aria-pressed", String(isPlaying));
  label.textContent = isPlaying ? "Pause Music" : "Play Music";
}

function initFlowerPetals() {
  const layer = document.querySelector("#petal-layer");
  if (!layer || state.reducedMotion || !window.gsap || state.petalTweens.length) return;
  const count = getPetalCount();
  for (let index = 0; index < count; index += 1) {
    const petal = document.createElement("span");
    petal.className = "petal";
    layer.appendChild(petal);
    animatePetal(petal, index * 0.22);
  }
}

function getPetalCount() {
  if (window.innerWidth < 560) return 10;
  if (window.innerWidth < 900) return 18;
  return 30;
}

function animatePetal(petal, delay = 0) {
  const palette = [
    ["#f8b7c8", "#b83252"],
    ["#f6d5d9", "#e75d74"],
    ["#cf3351", "#7d182f"],
    ["#ffe8b4", "#bf8d2c"]
  ];
  const colors = palette[Math.floor(Math.random() * palette.length)];
  const size = randomBetween(12, 28);
  const startX = randomBetween(-20, window.innerWidth + 20);
  const drift = randomBetween(-120, 120);
  const duration = randomBetween(8, 18);
  petal.style.setProperty("--petal-width", `${size}px`);
  petal.style.setProperty("--petal-height", `${size * randomBetween(1.28, 1.78)}px`);
  petal.style.setProperty("--petal-opacity", String(randomBetween(0.48, 0.84)));
  petal.style.setProperty("--petal-light", colors[0]);
  petal.style.setProperty("--petal-dark", colors[1]);
  window.gsap.set(petal, { x: startX, y: -80, rotation: randomBetween(-120, 120), scaleX: randomBetween(0.78, 1.15) });
  const fallTween = window.gsap.to(petal, {
    y: window.innerHeight + 110,
    x: startX + drift,
    rotation: `+=${randomBetween(180, 720)}`,
    duration,
    delay,
    ease: "none",
    onComplete: () => {
      state.petalTweens = state.petalTweens.filter((tween) => tween !== fallTween);
      animatePetal(petal, 0);
    }
  });
  state.petalTweens.push(fallTween);
}

function initHeroAnimation() {
  if (state.reducedMotion || !window.gsap) return;
  window.gsap.timeline()
    .from(".hero-media", { opacity: 0, scale: 1.08, duration: 1.1, ease: "power2.out" })
    .from(".hero-floral-mark", { opacity: 0, scale: 0.72, rotation: -18, duration: 0.9, ease: "power2.out" }, "-=0.55")
    .from(".hero-kicker", { opacity: 0, y: 22, duration: 0.55, ease: "power2.out" }, "-=0.25")
    .from("#hero-title span", { opacity: 0, y: 34, clipPath: "inset(0 0 100% 0)", duration: 0.68, stagger: 0.12, ease: "power2.out" }, "-=0.18")
    .from(".hero-message", { opacity: 0, y: 24, duration: 0.6, ease: "power2.out" }, "-=0.12");
}

function initScrollAnimations() {
  if (state.reducedMotion || !window.gsap) {
    if (window.AOS) window.AOS.init({ duration: 500, once: true, disable: true });
    return;
  }
  if (window.gsap.ScrollTrigger) window.gsap.registerPlugin(window.gsap.ScrollTrigger);
  if (window.AOS) window.AOS.init({ duration: 700, once: true, offset: 80, disable: () => window.innerWidth < 520 });
  if (!window.gsap.ScrollTrigger) return;

  window.gsap.utils.toArray(".section-heading").forEach((heading) => {
    window.gsap.from(heading.querySelectorAll(".eyebrow, h2"), {
      scrollTrigger: { trigger: heading, start: "top 82%" },
      y: 24,
      opacity: 0,
      duration: 0.75,
      stagger: 0.09,
      ease: "power2.out"
    });
  });
  window.gsap.from("[data-person-card='groom']", { scrollTrigger: { trigger: ".couple-section", start: "top 70%" }, x: -55, opacity: 0, duration: 0.9, ease: "power2.out" });
  window.gsap.from("[data-person-card='bride']", { scrollTrigger: { trigger: ".couple-section", start: "top 70%" }, x: 55, opacity: 0, duration: 0.9, ease: "power2.out" });
  window.gsap.to(".person-profile img", { y: -5, duration: 3.8, repeat: -1, yoyo: true, ease: "sine.inOut" });
  window.gsap.from(".ceremony-item", { scrollTrigger: { trigger: ".ceremony-list", start: "top 76%" }, y: 36, opacity: 0, scale: 0.96, duration: 0.75, stagger: 0.14, ease: "power2.out" });
  window.gsap.from(".family-group", { scrollTrigger: { trigger: ".family-section", start: "top 76%" }, y: 28, opacity: 0, duration: 0.72, stagger: 0.12, ease: "power2.out" });
  window.gsap.from(".gallery-item", { scrollTrigger: { trigger: ".gallery-grid", start: "top 76%" }, y: 22, opacity: 0, scale: 0.95, duration: 0.65, stagger: 0.08, ease: "power2.out" });
  window.gsap.from(".venue-content", { scrollTrigger: { trigger: ".venue-section", start: "top 72%" }, x: -42, opacity: 0, duration: 0.85, ease: "power2.out" });
  window.gsap.from(".map-frame", { scrollTrigger: { trigger: ".venue-section", start: "top 72%" }, x: 42, opacity: 0, duration: 0.85, ease: "power2.out" });
  window.gsap.from(".rsvp-form", { scrollTrigger: { trigger: ".rsvp-section", start: "top 74%" }, y: 28, opacity: 0, scale: 0.98, duration: 0.75, ease: "power2.out" });
  window.gsap.from(".section-divider span", { scrollTrigger: { trigger: ".section-divider", start: "top 88%" }, scaleX: 0, opacity: 0, duration: 0.85, stagger: 0.08, ease: "power2.out" });
}

function initStoryTimeline() {
  if (state.reducedMotion || !window.gsap || !window.gsap.ScrollTrigger) return;
  const timeline = document.querySelector("[data-story-timeline]");
  if (!timeline) return;
  window.gsap.fromTo("[data-story-line]", { scaleY: 0 }, {
    scaleY: 1,
    ease: "none",
    scrollTrigger: { trigger: timeline, start: "top 78%", end: "bottom 48%", scrub: true }
  });
  window.gsap.from(".story-milestone", { scrollTrigger: { trigger: timeline, start: "top 72%" }, y: 26, opacity: 0, duration: 0.7, stagger: 0.16, ease: "power2.out" });
  window.gsap.from(".story-dot", { scrollTrigger: { trigger: timeline, start: "top 72%" }, scale: 0, opacity: 0, duration: 0.46, stagger: 0.16, ease: "back.out(1.7)" });
}

function initGallery() {
  const lightbox = document.querySelector("[data-gallery-lightbox]");
  const image = document.querySelector("[data-lightbox-image]");
  const close = document.querySelector("[data-lightbox-close]");
  const prev = document.querySelector("[data-lightbox-prev]");
  const next = document.querySelector("[data-lightbox-next]");
  if (!lightbox || !image || !close || !prev || !next) return;
  document.querySelector(selectors.galleryList)?.addEventListener("click", (event) => {
    const button = event.target.closest("[data-gallery-image]");
    if (button) openLightbox(Number(button.dataset.galleryIndex || 0));
  });
  close.addEventListener("click", closeLightbox);
  prev.addEventListener("click", () => openLightbox(state.galleryIndex - 1));
  next.addEventListener("click", () => openLightbox(state.galleryIndex + 1));
  lightbox.addEventListener("click", (event) => {
    if (event.target === lightbox) closeLightbox();
  });
  document.addEventListener("keydown", (event) => {
    if (lightbox.hidden) return;
    if (event.key === "Escape") closeLightbox();
    if (event.key === "ArrowLeft") openLightbox(state.galleryIndex - 1);
    if (event.key === "ArrowRight") openLightbox(state.galleryIndex + 1);
  });
}

function openLightbox(index) {
  const lightbox = document.querySelector("[data-gallery-lightbox]");
  const image = document.querySelector("[data-lightbox-image]");
  if (!lightbox || !image || !weddingData.gallery.length) return;
  state.galleryIndex = (index + weddingData.gallery.length) % weddingData.gallery.length;
  const item = weddingData.gallery[state.galleryIndex];
  image.src = item.src;
  image.alt = item.alt || "Wedding gallery image";
  lightbox.hidden = false;
  if (!state.reducedMotion && window.gsap) {
    window.gsap.fromTo(lightbox, { opacity: 0 }, { opacity: 1, duration: 0.24, ease: "power2.out" });
    window.gsap.fromTo(image, { scale: 0.94 }, { scale: 1, duration: 0.32, ease: "power2.out" });
  }
}

function closeLightbox() {
  const lightbox = document.querySelector("[data-gallery-lightbox]");
  if (lightbox) lightbox.hidden = true;
}

function initCountdownScratch(dateValue) {
  const wrapper = document.querySelector(selectors.countdownScratchWrapper);
  const canvas = document.querySelector(selectors.countdownScratch);
  const content = document.querySelector(selectors.countdownContent);
  if (!wrapper || !canvas || !content || state.scratchInitialized) return;
  state.scratchInitialized = true;

  const context = canvas.getContext("2d", { willReadFrequently: true });
  if (!context) {
    completeCountdownScratch(dateValue);
    return;
  }

  let isScratching = false;
  let lastPoint = null;
  let scratchChecks = 0;

  const resizeCanvas = () => {
    if (state.scratchCompleted) return;
    const rect = wrapper.getBoundingClientRect();
    const pixelRatio = Math.min(window.devicePixelRatio || 1, 2);
    canvas.width = Math.max(1, Math.floor(rect.width * pixelRatio));
    canvas.height = Math.max(1, Math.floor(rect.height * pixelRatio));
    canvas.style.width = `${rect.width}px`;
    canvas.style.height = `${rect.height}px`;
    context.setTransform(pixelRatio, 0, 0, pixelRatio, 0, 0);
    paintScratchCard(context, rect.width, rect.height);
  };

  const scratchAt = (point) => {
    const brush = Math.max(28, Math.min(canvas.clientWidth, canvas.clientHeight) * 0.1);
    const gradient = context.createRadialGradient(point.x, point.y, 0, point.x, point.y, brush);
    gradient.addColorStop(0, "rgba(0, 0, 0, 1)");
    gradient.addColorStop(0.72, "rgba(0, 0, 0, 0.82)");
    gradient.addColorStop(1, "rgba(0, 0, 0, 0)");
    context.save();
    context.globalCompositeOperation = "destination-out";
    context.fillStyle = gradient;
    context.beginPath();
    context.arc(point.x, point.y, brush, 0, Math.PI * 2);
    context.fill();
    context.restore();
  };

  const scratchLine = (point) => {
    if (lastPoint) {
      const distance = Math.hypot(point.x - lastPoint.x, point.y - lastPoint.y);
      const steps = Math.max(1, Math.ceil(distance / 14));
      for (let step = 1; step <= steps; step += 1) {
        scratchAt({
          x: lastPoint.x + ((point.x - lastPoint.x) * step) / steps,
          y: lastPoint.y + ((point.y - lastPoint.y) * step) / steps
        });
      }
    } else {
      scratchAt(point);
    }
    lastPoint = point;
    scratchChecks += 1;
    if (scratchChecks % 4 === 0 && getScratchClearedRatio(context, canvas) >= 0.45) {
      completeCountdownScratch(dateValue);
    }
  };

  const getPoint = (event) => {
    const rect = canvas.getBoundingClientRect();
    return {
      x: event.clientX - rect.left,
      y: event.clientY - rect.top
    };
  };

  canvas.addEventListener("pointerdown", (event) => {
    if (state.scratchCompleted) return;
    isScratching = true;
    lastPoint = null;
    canvas.classList.add("is-scratching");
    canvas.setPointerCapture?.(event.pointerId);
    scratchLine(getPoint(event));
  });

  canvas.addEventListener("pointermove", (event) => {
    if (!isScratching || state.scratchCompleted) return;
    event.preventDefault();
    scratchLine(getPoint(event));
  });

  const stopScratch = () => {
    if (!state.scratchCompleted && getScratchClearedRatio(context, canvas) >= 0.45) {
      completeCountdownScratch(dateValue);
    }
    isScratching = false;
    lastPoint = null;
    canvas.classList.remove("is-scratching");
  };

  canvas.addEventListener("pointerup", stopScratch);
  canvas.addEventListener("pointercancel", stopScratch);
  canvas.addEventListener("pointerleave", stopScratch);
  window.addEventListener("resize", resizeCanvas, { passive: true });
  resizeCanvas();
}

function paintScratchCard(context, width, height) {
  context.clearRect(0, 0, width, height);
  const gradient = context.createLinearGradient(0, 0, width, height);
  gradient.addColorStop(0, "#8f1d38");
  gradient.addColorStop(0.42, "#d44858");
  gradient.addColorStop(1, "#bf8d2c");
  context.fillStyle = gradient;
  context.fillRect(0, 0, width, height);

  context.fillStyle = "rgba(255, 248, 236, 0.16)";
  for (let x = -height; x < width; x += 34) {
    context.beginPath();
    context.moveTo(x, height);
    context.lineTo(x + height, 0);
    context.lineTo(x + height + 12, 0);
    context.lineTo(x + 12, height);
    context.closePath();
    context.fill();
  }

  context.fillStyle = "rgba(255, 253, 247, 0.92)";
  context.textAlign = "center";
  context.textBaseline = "middle";
  context.font = `700 ${Math.max(24, Math.min(40, width * 0.058))}px "Cormorant Garamond", Georgia, serif`;
  context.fillText("Scratch to Reveal the Wedding Date", width / 2, height / 2 - 8);
  context.font = `700 ${Math.max(11, Math.min(14, width * 0.026))}px "Inter", system-ui, sans-serif`;
  context.letterSpacing = "0.18em";
  context.fillText("AND THE COUNTDOWN", width / 2, height / 2 + 36);
}

function getScratchClearedRatio(context, canvas) {
  const pixels = context.getImageData(0, 0, canvas.width, canvas.height).data;
  let cleared = 0;
  for (let index = 3; index < pixels.length; index += 4) {
    if (pixels[index] < 32) cleared += 1;
  }
  return cleared / (pixels.length / 4);
}

function completeCountdownScratch(dateValue) {
  if (state.scratchCompleted) return;
  state.scratchCompleted = true;
  const wrapper = document.querySelector(selectors.countdownScratchWrapper);
  const canvas = document.querySelector(selectors.countdownScratch);
  const content = document.querySelector(selectors.countdownContent);
  wrapper?.classList.add("is-revealed");
  triggerCountdownPetalBurst();
  startCountdown(dateValue);
  if (window.gsap && !state.reducedMotion) {
    window.gsap.to(canvas, { opacity: 0, duration: 0.65, ease: "power2.out", onComplete: () => { if (canvas) canvas.hidden = true; } });
    window.gsap.to(content, { autoAlpha: 1, y: 0, duration: 0.72, delay: 0.18, ease: "power2.out" });
  } else {
    if (canvas) canvas.hidden = true;
    if (content) {
      content.style.opacity = "1";
      content.style.transform = "translateY(0)";
      content.style.visibility = "visible";
    }
  }
}

function triggerCountdownPetalBurst() {
  const layer = document.querySelector(selectors.countdownBurstLayer);
  if (!layer || state.reducedMotion || !window.gsap) return;
  const rect = layer.getBoundingClientRect();
  const count = window.innerWidth < 540 ? 42 : 64;
  for (let index = 0; index < count; index += 1) {
    const petal = document.createElement("span");
    const size = randomBetween(13, 28);
    petal.className = "petal";
    petal.style.setProperty("--petal-width", `${size}px`);
    petal.style.setProperty("--petal-height", `${size * randomBetween(1.3, 1.75)}px`);
    petal.style.setProperty("--petal-opacity", String(randomBetween(0.66, 0.95)));
    petal.style.setProperty("--petal-light", "#ff9aad");
    petal.style.setProperty("--petal-dark", "#9f2443");
    layer.appendChild(petal);

    const angle = randomBetween(0, Math.PI * 2);
    const distance = randomBetween(Math.min(rect.width, rect.height) * 0.18, Math.max(rect.width, rect.height) * 0.72);
    window.gsap.fromTo(petal, {
      x: -size / 2,
      y: -size / 2,
      scale: randomBetween(0.65, 1.1),
      rotation: randomBetween(-90, 90),
      opacity: randomBetween(0.7, 1)
    }, {
      x: Math.cos(angle) * distance - size / 2,
      y: Math.sin(angle) * distance - size / 2,
      scale: randomBetween(0.7, 1.45),
      rotation: `+=${randomBetween(260, 860)}`,
      opacity: 0,
      duration: randomBetween(1.2, 2.3),
      ease: "power2.out",
      onComplete: () => petal.remove()
    });
  }
}

function startCountdown(dateValue) {
  if (state.countdownStarted) return;
  state.countdownStarted = true;
  initCountdown(dateValue);
}

function initCountdown(dateValue) {
  const target = document.querySelector(selectors.countdown);
  if (!target || !dateValue) return;
  const date = new Date(dateValue);
  const fields = {
    days: target.querySelector("[data-countdown-days]"),
    hours: target.querySelector("[data-countdown-hours]"),
    minutes: target.querySelector("[data-countdown-minutes]"),
    seconds: target.querySelector("[data-countdown-seconds]")
  };
  if (Object.values(fields).some((field) => !field)) return;
  const update = () => {
    const remaining = Math.max(0, date.getTime() - Date.now());
    const totalSeconds = Math.floor(remaining / 1000);
    fields.days.textContent = String(Math.floor(totalSeconds / 86400)).padStart(2, "0");
    fields.hours.textContent = String(Math.floor((totalSeconds % 86400) / 3600)).padStart(2, "0");
    fields.minutes.textContent = String(Math.floor((totalSeconds % 3600) / 60)).padStart(2, "0");
    fields.seconds.textContent = String(totalSeconds % 60).padStart(2, "0");
  };
  update();
  state.countdownIntervalId = window.setInterval(update, 1000);
}

function initRSVPValidation() {
  const form = document.querySelector(selectors.rsvpForm);
  const feedback = document.querySelector(selectors.rsvpFeedback);
  if (!form || !feedback) return;
  form.addEventListener("submit", async (event) => {
    event.preventDefault();
    feedback.classList.remove("is-error");
    if (!form.checkValidity()) {
      feedback.textContent = "Please complete the required RSVP details.";
      feedback.classList.add("is-error");
      form.reportValidity();
      return;
    }
    const payload = Object.fromEntries(new FormData(form).entries());
    try {
      await submitRsvp(payload);
      feedback.textContent = "Thank you. Your RSVP has been received for this demo.";
      form.reset();
    } catch (error) {
      feedback.textContent = "We could not submit your RSVP. Please try again.";
      feedback.classList.add("is-error");
    }
  });
}

async function submitRsvp(payload) {
  console.info("RSVP demo payload", payload);
  await new Promise((resolve) => window.setTimeout(resolve, 350));
  return { ok: true };
}

function initThreeBackground() {
  const canvas = document.querySelector("#mandala-canvas");
  if (!canvas || state.reducedMotion || !window.THREE || window.innerWidth < 768) return;
  state.threeRenderer = new window.THREE.WebGLRenderer({ canvas, alpha: true, antialias: true });
  state.threeRenderer.setPixelRatio(Math.min(window.devicePixelRatio, 1.5));
  state.threeScene = new window.THREE.Scene();
  state.threeCamera = new window.THREE.PerspectiveCamera(45, 1, 0.1, 100);
  state.threeCamera.position.z = 18;
  state.threeGroup = new window.THREE.Group();
  const material = new window.THREE.PointsMaterial({ color: 0xffd784, size: 0.07, transparent: true, opacity: 0.55 });
  const points = [];
  for (let index = 0; index < 120; index += 1) {
    points.push(new window.THREE.Vector3(randomBetween(-9, 9), randomBetween(-5, 5), randomBetween(-2, 2)));
  }
  const geometry = new window.THREE.BufferGeometry().setFromPoints(points);
  state.threeGroup.add(new window.THREE.Points(geometry, material));
  state.threeScene.add(state.threeGroup);
  const resize = () => {
    const width = canvas.clientWidth || window.innerWidth;
    const height = canvas.clientHeight || window.innerHeight;
    state.threeRenderer.setSize(width, height, false);
    state.threeCamera.aspect = width / height;
    state.threeCamera.updateProjectionMatrix();
  };
  window.addEventListener("resize", resize, { passive: true });
  resize();
  startThreeRender();
}

function startThreeRender() {
  if (state.threeAnimationId || !state.threeRenderer || !state.threeScene || !state.threeCamera || !state.threeGroup) return;
  const render = () => {
    state.threeGroup.rotation.z += 0.0006;
    state.threeGroup.rotation.y += 0.00035;
    state.threeRenderer.render(state.threeScene, state.threeCamera);
    state.threeAnimationId = window.requestAnimationFrame(render);
  };
  render();
}

function stopThreeRender() {
  if (!state.threeAnimationId) return;
  window.cancelAnimationFrame(state.threeAnimationId);
  state.threeAnimationId = null;
}

function initParticles() {
  if (state.reducedMotion || !window.particlesJS) return;
  const count = window.innerWidth < 768 ? 12 : 28;
  window.particlesJS("particles-js", {
    particles: {
      number: { value: count, density: { enable: true, value_area: 900 } },
      color: { value: "#ffd784" },
      shape: { type: "circle" },
      opacity: { value: 0.22, random: true },
      size: { value: 2, random: true },
      line_linked: { enable: false },
      move: { enable: true, speed: 0.35, direction: "top", random: true, out_mode: "out" }
    },
    interactivity: { events: { resize: true } },
    retina_detect: true
  });
}

function initMobileNavigation() {
  const toggle = document.querySelector(".nav-toggle");
  const menu = document.querySelector("#primary-menu");
  if (!toggle || !menu) return;
  toggle.addEventListener("click", () => {
    const isOpen = toggle.getAttribute("aria-expanded") === "true";
    toggle.setAttribute("aria-expanded", String(!isOpen));
    document.body.classList.toggle("nav-open", !isOpen);
  });
  menu.addEventListener("click", (event) => {
    if (event.target.closest("a")) {
      toggle.setAttribute("aria-expanded", "false");
      document.body.classList.remove("nav-open");
    }
  });
}

function initPageVisibilityPerformance() {
  document.addEventListener("visibilitychange", () => {
    if (document.hidden) {
      state.petalTweens.forEach((tween) => tween.pause());
      state.petalPaused = true;
      stopThreeRender();
      return;
    }
    if (state.petalPaused) {
      state.petalTweens.forEach((tween) => tween.resume());
      state.petalPaused = false;
    }
    startThreeRender();
  });
}

function randomBetween(min, max) {
  return min + Math.random() * (max - min);
}
