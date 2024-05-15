// theme-switch.js
console.log('theme-switch.js loaded'); // Ajoutez cette ligne

// Récupérer l'élément de changement de thème
var themeSwitch = document.getElementById('theme-switch');

// Vérifier si un thème a été sauvegardé dans le stockage local
if(localStorage.getItem('theme') === 'dark') {
  document.body.classList.add('dark-theme');
  themeSwitch.checked = true;
} else {
  document.body.classList.add('light-theme');
}

// Ajouter un listener d'événements pour changer de thème
themeSwitch.addEventListener('change', function(event) {
  // Basculer entre les thèmes
  document.body.classList.toggle('dark-theme', event.target.checked);
  document.body.classList.toggle('light-theme', !event.target.checked);

  // Sauvegarder le thème dans le stockage local
  localStorage.setItem('theme', event.target.checked ? 'dark' : 'light');
});
