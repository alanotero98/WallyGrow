(() => {
	"use strict";

	const config = window.wallyProductSearch || {};
	const forms = document.querySelectorAll("[data-wg-product-search]");

	if (!forms.length || !config.endpoint) {
		return;
	}

	forms.forEach((form) => {
		const input = form.querySelector(".wg-product-search__input");
		const dropdown = form.querySelector(".wg-product-search__dropdown");
		const live = form.querySelector(".wg-product-search__live");
		const minimum = Number(config.minChars) || 2;
		let debounceTimer = 0;
		let controller = null;
		let activeIndex = -1;
		let options = [];

		const announce = (message) => {
			live.textContent = message;
		};

		const setExpanded = (expanded) => {
			input.setAttribute("aria-expanded", String(expanded));
			dropdown.hidden = !expanded;
			if (!expanded) {
				input.removeAttribute("aria-activedescendant");
				activeIndex = -1;
			}
		};

		const close = () => {
			setExpanded(false);
		};

		const showMessage = (message, action = null) => {
			dropdown.replaceChildren();
			const paragraph = document.createElement("p");
			paragraph.className = "wg-product-search__message";
			paragraph.setAttribute("role", "status");
			paragraph.textContent = message;
			dropdown.append(paragraph);

			if (action?.url && action?.label) {
				const link = document.createElement("a");
				link.className = "wg-product-search__message-action";
				link.href = action.url;
				link.textContent = action.label;
				dropdown.append(link);
			}

			options = [];
			setExpanded(true);
			announce(message);
		};

		const showMinimumMessage = () => {
			showMessage(
				config.messages?.tooShort || "Escribí al menos 2 caracteres para buscar.",
			);
			input.focus();
		};

		const renderResults = (products) => {
			dropdown.replaceChildren();
			activeIndex = -1;

			if (!products.length) {
				showMessage(config.messages?.empty || "No encontramos productos.", {
					url: config.shopUrl,
					label: config.messages?.browseShop || "Ver toda la tienda",
				});
				return;
			}

			products.slice(0, 8).forEach((product, index) => {
				const option = document.createElement("a");
				const optionId = `${dropdown.id}-option-${index}`;
				option.id = optionId;
				option.className = "wg-product-search__option";
				option.href = product.url;
				option.setAttribute("role", "option");
				option.setAttribute("aria-selected", "false");

				const image = document.createElement("img");
				image.className = "wg-product-search__image";
				image.src = product.image;
				image.alt = "";
				image.loading = "lazy";
				image.width = 58;
				image.height = 58;

				const content = document.createElement("span");
				const name = document.createElement("span");
				name.className = "wg-product-search__name";
				name.textContent = product.name;
				content.append(name);

				if (product.category) {
					const category = document.createElement("span");
					category.className = "wg-product-search__meta";
					category.textContent = product.category;
					content.append(category);
				}

				if (product.stock) {
					const stock = document.createElement("span");
					stock.className = `wg-product-search__stock${product.inStock ? "" : " is-out"}`;
					stock.textContent = product.stock;
					content.append(stock);
				}

				const price = document.createElement("span");
				price.className = "wg-product-search__price";
				price.textContent = product.price;

				option.append(image, content, price);
				dropdown.append(option);
			});

			options = Array.from(dropdown.querySelectorAll('[role="option"]'));
			setExpanded(true);
			announce(`${options.length} resultados disponibles.`);
		};

		const setActive = (nextIndex) => {
			if (!options.length) {
				return;
			}

			activeIndex = (nextIndex + options.length) % options.length;
			options.forEach((option, index) => {
				const active = index === activeIndex;
				option.classList.toggle("is-active", active);
				option.setAttribute("aria-selected", String(active));
			});
			input.setAttribute("aria-activedescendant", options[activeIndex].id);
			options[activeIndex].scrollIntoView({ block: "nearest" });
		};

		const search = async (term) => {
			if (controller) {
				controller.abort();
			}

			controller = new AbortController();
			const requestController = controller;
			form.classList.add("is-loading");
			announce(config.messages?.loading || "Buscando productos…");

			try {
				const url = new URL(config.endpoint);
				url.searchParams.set("q", term);
				const response = await fetch(url.toString(), {
					signal: requestController.signal,
					headers: { Accept: "application/json" },
				});

				if (!response.ok) {
					throw new Error(`HTTP ${response.status}`);
				}

				const products = await response.json();
				if (input.value.trim() === term) {
					renderResults(Array.isArray(products) ? products : []);
				}
			} catch (error) {
				if (error.name !== "AbortError") {
					showMessage(config.messages?.error || "No pudimos completar la búsqueda.");
				}
			} finally {
				if (!requestController.signal.aborted && controller === requestController) {
					form.classList.remove("is-loading");
				}
			}
		};

		input.addEventListener("input", () => {
			window.clearTimeout(debounceTimer);
			const term = input.value.trim();

			if (controller) {
				controller.abort();
			}
			form.classList.remove("is-loading");

			if (term.length < minimum) {
				dropdown.replaceChildren();
				close();
				announce("");
				return;
			}

			debounceTimer = window.setTimeout(() => search(term), 300);
		});

		input.addEventListener("keydown", (event) => {
			if (event.key === "ArrowDown") {
				event.preventDefault();
				if (dropdown.hidden && options.length) {
					setExpanded(true);
				}
				setActive(activeIndex + 1);
			} else if (event.key === "ArrowUp") {
				event.preventDefault();
				setActive(activeIndex - 1);
			} else if (event.key === "Enter" && activeIndex >= 0 && options[activeIndex]) {
				event.preventDefault();
				window.location.assign(options[activeIndex].href);
			} else if (event.key === "Escape") {
				event.preventDefault();
				close();
			}
		});

		form.addEventListener("submit", (event) => {
			if (input.value.trim().length < minimum) {
				event.preventDefault();
				showMinimumMessage();
			}
		});

		input.addEventListener("invalid", (event) => {
			event.preventDefault();
			showMinimumMessage();
		});

		document.addEventListener("pointerdown", (event) => {
			if (!form.contains(event.target)) {
				close();
			}
		});

		input.addEventListener("focus", () => {
			if (options.length || dropdown.querySelector(".wg-product-search__message")) {
				setExpanded(true);
			}
		});
	});
})();
