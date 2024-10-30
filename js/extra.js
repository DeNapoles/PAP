// Mostrar o botão quando o utilizador faz scroll para baixo
window.onscroll = function() {scrollFunction()};

function scrollFunction() {
  const button = document.getElementById("backToTopBtn");
  if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) {
    button.style.display = "block";
  } else {
    button.style.display = "none";
  }
}

// Função para voltar ao topo da página
function topFunction() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}