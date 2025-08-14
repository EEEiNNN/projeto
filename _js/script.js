document.addEventListener('DOMContentLoaded', () => {
  const cartToggle = document.getElementById('cartToggle');
  const cartSidebar = document.getElementById('cartSidebar');
  const closeCart = document.getElementById('closeCart');

  if (cartToggle && cartSidebar && closeCart) {
    cartToggle.addEventListener('click', (e) => {
      e.preventDefault();
      cartSidebar.classList.add('open');
    });

    closeCart.addEventListener('click', () => {
      cartSidebar.classList.remove('open');
    });

    // Fechar ao clicar fora do carrinho
    document.addEventListener('click', (e) => {
      const isClickInside = cartSidebar.contains(e.target) || cartToggle.contains(e.target);
      if (!isClickInside) {
        cartSidebar.classList.remove('open');
      }
    });
  }
});
