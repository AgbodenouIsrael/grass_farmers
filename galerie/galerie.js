// Filtrage simple JS
    const buttons = document.querySelectorAll('.filter-btn');
    const cards = document.querySelectorAll('.card');

    buttons.forEach(btn => btn.addEventListener('click', () => {
      buttons.forEach(b=>b.classList.remove('active'));
      btn.classList.add('active');
      const filter = btn.dataset.filter;
      if(filter==='all'){
        cards.forEach(c=>c.style.display='block');
        return;
      }
      cards.forEach(c=>{
        const cat = c.dataset.category;
        c.style.display = (cat===filter) ? 'block' : 'none';
      })
    }));

 document.addEventListener('DOMContentLoaded', () => {
 const images = document.querySelectorAll('.card img');
 const lightbox = document.getElementById('lightbox');
 const lightboxImg = document.getElementById('lightbox-img');
 const closeBtn = document.querySelector('.close-btn');

 // --- Logique d'ouverture de la lightbox ---
 images.forEach(image => {
 image.addEventListener('click', () => {
 // 1. Ouvre la lightbox
 lightbox.style.display = 'block';

 // 2. Charge la source de l'image cliquée dans la lightbox
 lightboxImg.src = image.src;
 lightboxImg.alt = image.alt;

 // 3. Ajoute une classe au body pour bloquer le défilement en arrière-plan
 document.body.classList.add('lightbox-open');
 });
 });

 // --- Logique de fermeture de la lightbox ---

 // 1. Fermeture via le bouton 'X'
 closeBtn.addEventListener('click', () => {
 lightbox.style.display = 'none';
  document.body.classList.remove('lightbox-open');
 });

 // 2. Fermeture en cliquant n'importe où sur l'arrière-plan assombri
 lightbox.addEventListener('click', (e) => {
 // Vérifie si le clic a eu lieu sur l'élément lightbox (l'arrière-plan) et non sur l'image elle-même ou le bouton
 if (e.target === lightbox) {
  lightbox.style.display = 'none';
  document.body.classList.remove('lightbox-open');
  }
 });

 // 3. Fermeture avec la touche Échap (Esc) du clavier
 document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape' && lightbox.style.display === 'block') {
 lightbox.style.display = 'none';
   document.body.classList.remove('lightbox-open');
  }
 });


 // --- Logique de filtrage existante (à conserver si elle est déjà dans galerie.js) ---
 // Assurez-vous que le reste de votre code JavaScript pour les filtres (data-filter) est toujours présent
 
 // Si le code des filtres n'existe pas encore, vous pouvez l'ajouter ici:
 const filterButtons = document.querySelectorAll('.filter-btn');
 const galleryCards = document.querySelectorAll('.card');

 filterButtons.forEach(button => {
 button.addEventListener('click', () => {
 // Supprime la classe active de tous les boutons
 filterButtons.forEach(btn => btn.classList.remove('active'));
 // Ajoute la classe active au bouton cliqué
   button.classList.add('active');

 const filter = button.dataset.filter;

 galleryCards.forEach(card => {
 const category = card.dataset.category;
 
 if (filter === 'all' || category === filter) {
 card.style.display = 'block'; // Affiche l'élément
 }else {
 card.style.display = 'none'; // Cache l'élément
 }
 });
 });
 });
});
   
