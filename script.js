document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('.contact-form'); // ✅ Ensure this matches your actual form class or ID
    if (!form) {
        console.error("❌ Form element not found! Check the class or ID.");
        return;
    }

    const submitBtn = form.querySelector('button[type="submit"]');

    function showPopup(type, message) {
        let oldPopup = document.querySelector(`.popup.${type}-popup`);
        if (oldPopup) oldPopup.remove();

        let popup = document.createElement("div");
        popup.className = `popup ${type}-popup`;
        popup.innerHTML = `
            <div class="popup-content">
                <span class="close">&times;</span>
                <p>${message}</p>
            </div>
        `;
        document.body.appendChild(popup);

        setTimeout(() => popup.classList.add("active"), 100);
        popup.querySelector(".close").addEventListener("click", () => popup.remove());
        setTimeout(() => popup.remove(), 3000);
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault(); // ✅ Prevents the page from reloading
        submitBtn.disabled = true;
        submitBtn.textContent = 'Sending...';

        try {
            const formData = new FormData(form);

            console.log("🔥 Form Data Sent: ", Object.fromEntries(formData)); // ✅ Debugging check

            const response = await fetch(form.action, {
                method: 'POST',
                headers: { "Accept": "application/json" },
                body: formData
            });

            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

            const data = await response.json();

            console.log("🔥 JSON Response: ", data); // ✅ Debugging check

            showPopup("success", data.message);

            if (data.success) {
                form.reset();
            } else {
                showPopup("error", "Eish, something went wrong! 😅");
            }

        } catch (error) {
            showPopup("error", error.message || "Failed to send message");
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Send Message';
        }
    });
});