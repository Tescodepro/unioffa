{{-- Shared JS for payment setting forms: Select2, installment toggling, live % total --}}
<script>
    $(document).ready(function () {

        // ── Select2 ──────────────────────────────────────────────────────────
        $('.select2-multi').select2({
            placeholder: 'Select (leave empty for ALL)',
            allowClear: true,
            width: '100%',
        });

        // ── Installment toggle ────────────────────────────────────────────────
        const allowToggle = document.getElementById('installmental_allow_status');
        const installSections = document.querySelectorAll('.installment-section');
        const numberInput = document.getElementById('number_of_instalment');
        const pctContainer = document.getElementById('instalmentPercentages');
        const totalSpan = document.getElementById('totalPct');

        function updateTotal() {
            const inputs = pctContainer.querySelectorAll('.pct-input');
            let sum = 0;
            inputs.forEach(inp => sum += parseFloat(inp.value) || 0);
            totalSpan.textContent = Math.round(sum * 10) / 10;
            const alert = document.getElementById('percentTotal');
            if (alert) {
                alert.className = 'alert py-2 px-3 mb-0 w-100 small ' +
                    (Math.round(sum) === 100 ? 'alert-success' : 'alert-warning');
            }
        }

        function generatePercentageInputs() {
            pctContainer.innerHTML = '';
            const count = parseInt(numberInput.value) || 2;
            const equalSplit = Math.floor(100 / count);
            for (let i = 0; i < count; i++) {
                const val = i === count - 1 ? (100 - equalSplit * (count - 1)) : equalSplit;
                const div = document.createElement('div');
                div.className = 'col-md-2 col-4';
                div.innerHTML = `
                <label class="text-muted small">Part ${i + 1}</label>
                <div class="input-group input-group-sm">
                    <input type="number" name="list_instalment_percentage[]"
                        class="form-control pct-input" value="${val}" step="0.1" min="1" max="100">
                    <span class="input-group-text">%</span>
                </div>`;
                pctContainer.appendChild(div);
            }
            pctContainer.querySelectorAll('.pct-input').forEach(inp => inp.addEventListener('input', updateTotal));
            updateTotal();
        }

        function toggleInstallment() {
            const show = allowToggle.value === '1';
            installSections.forEach(sec => sec.classList.toggle('d-none', !show));
            if (show) {
                numberInput.removeAttribute('disabled');
                if (pctContainer.children.length === 0) { generatePercentageInputs(); }
                updateTotal();
            } else {
                numberInput.setAttribute('disabled', 'disabled');
                pctContainer.innerHTML = '';
            }
        }

        allowToggle.addEventListener('change', toggleInstallment);
        numberInput.addEventListener('input', generatePercentageInputs);

        // Attach existing % inputs (edit mode)
        pctContainer.querySelectorAll('.pct-input').forEach(inp => inp.addEventListener('input', updateTotal));

        // Initial state
        if (allowToggle.value === '1') {
            numberInput.removeAttribute('disabled');
            updateTotal();
        } else {
            numberInput.setAttribute('disabled', 'disabled');
        }
    });
</script>