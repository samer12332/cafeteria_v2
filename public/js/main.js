(() => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  const setError = (field, message) => {
    const container = field.closest("[data-field]") || field.parentElement;
    if (!container) return;

    const errorEl = container.querySelector("[data-error]");
    if (errorEl) {
      errorEl.textContent = message;
      errorEl.classList.toggle("hidden", !message);
    }

    field.classList.toggle("border-red-500", !!message);
    field.classList.toggle("ring-red-200", !!message);
  };

  const validateField = (field) => {
    const value = (field.value || "").trim();
    const isRequired = field.hasAttribute("required");

    if (isRequired && !value) {
      return "This field is required.";
    }

    const type = field.getAttribute("data-validate");

    if (type === "email" && value && !emailRegex.test(value)) {
      return "Please enter a valid email address.";
    }

    if (type === "min" && value) {
      const minLen = parseInt(field.getAttribute("data-min") || "1", 10);
      if (value.length < minLen) {
        return `Please enter at least ${minLen} characters.`;
      }
    }

    return "";
  };

  document.querySelectorAll("[data-validate-form]").forEach((form) => {
    form.addEventListener("submit", (event) => {
      const fields = form.querySelectorAll("input, textarea, select");
      let hasError = false;

      fields.forEach((field) => {
        if (field.disabled || field.type === "file") return;
        const message = validateField(field);
        setError(field, message);
        if (message) hasError = true;
      });

      if (form.hasAttribute("data-require-cart")) {
        const cartScope = form.closest("[data-cart]");
        const cartItems = cartScope ? cartScope.querySelectorAll("[data-cart-item]") : [];
        if (!cartItems.length) {
          hasError = true;
        }
      }

      const alertEl = form.querySelector("[data-form-alert]");
      if (alertEl) {
        alertEl.classList.toggle("hidden", !hasError);
        if (hasError && !form.querySelector("[data-error]:not(.hidden)")) {
          alertEl.textContent = "Please add at least one item to the cart.";
        }
      }

      if (hasError) {
        event.preventDefault();
      }
    });
  });

  const currency = (value) => `EGP ${value}`;
  const cartRoot = document.querySelector("[data-cart]");
  const menuRoot = document.querySelector("[data-menu]");
  const searchInput = document.querySelector("[data-search]");

  if (!cartRoot || !menuRoot) return;

  const cartItemsEl = cartRoot.querySelector("[data-cart-items]");
  const cartTotalEl = cartRoot.querySelector("[data-cart-total]");
  const cartPayloadEl = cartRoot.querySelector("[data-cart-payload]");
  const notesEl = cartRoot.querySelector("textarea");
  const roomEl = cartRoot.querySelector("select");
  const productCards = Array.from(menuRoot.querySelectorAll("[data-product]"));
  const products = productCards.map((card) => ({
    id: card.getAttribute("data-id"),
    name: card.getAttribute("data-name"),
    price: parseFloat(card.getAttribute("data-price") || "0"),
    available: card.getAttribute("data-available") === "1",
  }));
  const cart = new Map();

  const renderCart = () => {
    if (!cartItemsEl) return;
    cartItemsEl.innerHTML = "";

    if (!cart.size) {
      const empty = document.createElement("p");
      empty.className = "py-6 text-center text-sm text-slate-500";
      empty.textContent = "No items yet. Select a product.";
      cartItemsEl.appendChild(empty);
    }

    let total = 0;

    cart.forEach((item) => {
      total += item.price * item.qty;

      const row = document.createElement("div");
      row.className =
        "flex items-center justify-between rounded-2xl border border-orange-100 bg-white px-4 py-3";
      row.setAttribute("data-cart-item", item.id);
      row.innerHTML = `
        <div>
          <p class="font-medium">${item.name}</p>
          <p class="text-xs text-slate-500">${item.note || "No notes added"}</p>
        </div>
        <div class="flex items-center gap-3">
          <div class="flex items-center gap-2 rounded-full bg-orange-50 px-3 py-1 text-sm">
            <button class="text-brand-600" data-action="dec" data-id="${item.id}" type="button">-</button>
            <span>${item.qty}</span>
            <button class="text-brand-600" data-action="inc" data-id="${item.id}" type="button">+</button>
          </div>
          <span class="font-semibold text-slate-700">${currency(item.price * item.qty)}</span>
          <button class="text-xs text-red-500" data-action="remove" data-id="${item.id}" type="button">Remove</button>
        </div>
      `;

      cartItemsEl.appendChild(row);
    });

    if (cartTotalEl) {
      cartTotalEl.textContent = currency(total);
    }

    if (cartPayloadEl) {
      cartPayloadEl.value = JSON.stringify(
        Array.from(cart.values()).map((item) => ({
          id: item.id,
          qty: item.qty,
        }))
      );
    }
  };

  const updateCart = (product, delta) => {
    if (!product) return;

    const existing = cart.get(product.id) || { ...product, qty: 0, note: "" };
    existing.qty += delta;
    existing.note = (notesEl?.value || "").trim();

    if (existing.qty <= 0) {
      cart.delete(product.id);
    } else {
      cart.set(product.id, existing);
    }

    renderCart();
  };

  menuRoot.addEventListener("click", (event) => {
    const card = event.target.closest("[data-product]");
    if (!card) return;
    if (card.getAttribute("data-available") !== "1") return;

    const productId = card.getAttribute("data-id");
    const product = products.find((item) => item.id === productId && item.available);
    updateCart(product, 1);
  });

  cartRoot.addEventListener("click", (event) => {
    const actionBtn = event.target.closest("[data-action]");
    if (!actionBtn) return;

    const id = actionBtn.getAttribute("data-id");
    const product = products.find((item) => item.id === id) || cart.get(id);
    if (!product) return;

    const action = actionBtn.getAttribute("data-action");
    if (action === "inc") updateCart(product, 1);
    if (action === "dec") updateCart(product, -1);
    if (action === "remove") {
      cart.delete(id);
      renderCart();
    }
  });

  if (notesEl) {
    notesEl.addEventListener("input", () => {
      cart.forEach((item) => {
        item.note = notesEl.value.trim();
      });
      renderCart();
    });
  }

  if (roomEl) {
    roomEl.addEventListener("change", () => roomEl.classList.remove("border-red-500"));
  }

  if (searchInput) {
    searchInput.addEventListener("input", () => {
      const query = searchInput.value.trim().toLowerCase();
      productCards.forEach((card) => {
        const name = (card.getAttribute("data-name") || "").toLowerCase();
        const visible = !query || name.includes(query);
        card.classList.toggle("hidden", !visible);
      });
    });
  }

  renderCart();
})();
