document.addEventListener("DOMContentLoaded", () => {
    const dateInput = document.getElementById("date");
    const hairdresserSelect = document.getElementById("hairdresser_id");
    const hourSelect = document.getElementById("hour");
    const form = document.querySelector("form[data-availability-url]");

    if (!dateInput || !hairdresserSelect || !hourSelect || !form) return;

    if (typeof Datepicker !== "undefined") {
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        new Datepicker(dateInput, {
            autohide: true,
            format: "yyyy-mm-dd",
            minDate: today,
            daysOfWeekDisabled: [0, 6],
            todayHighlight: true,
            buttonClass: "btn",
        });
    }

    const url = form.dataset.availabilityUrl;

    function resetHours() {
        [...hourSelect.options].forEach((opt) => {
            if (opt.value) opt.disabled = false;
        });
    }

    async function refreshHours() {
        resetHours();

        const date = dateInput.value;
        const hairdresserId = hairdresserSelect.value;
        if (!date || !hairdresserId) return;

        try {
            const res = await fetch(
                `${url}?date=${encodeURIComponent(
                    date
                )}&hairdresser_id=${encodeURIComponent(hairdresserId)}`,
                { headers: { "X-Requested-With": "XMLHttpRequest" } }
            );

            if (!res.ok) {
                console.error("Availability request failed", res.status);
                return;
            }

            const data = await res.json();
            const taken = new Set((data.taken_hours || []).map(String));

            [...hourSelect.options].forEach((opt) => {
                if (!opt.value) return;
                opt.disabled = taken.has(opt.value);
            });

            if (hourSelect.value && taken.has(hourSelect.value)) {
                hourSelect.value = "";
            }
        } catch (error) {
            console.error("Failed to load availability", error);
        }
    }

    dateInput.addEventListener("changeDate", refreshHours);
    dateInput.addEventListener("change", refreshHours);
    hairdresserSelect.addEventListener("change", refreshHours);

    refreshHours();
});
