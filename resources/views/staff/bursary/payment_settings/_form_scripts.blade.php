{{-- Shared JS for payment setting forms: Select2, installment toggling, live % total --}}
<script>
    $(document).ready(function () {

        // ── Select2 Initialization ──────────────────────────────────────────
        $('.select2-multi').select2({
            placeholder: 'All (Selective)',
            allowClear: true,
            width: '100%',
        });

        // ── Installment Logic ───────────────────────────────────────────────
        const toggleSwitch = document.getElementById('toggle_installments');
        const hiddenStatus = document.getElementById('installmental_allow_status_hidden');
        const installSections = document.querySelectorAll('.installment-section');
        const fullPaymentNote = document.getElementById('full_payment_note');
        const numberInput = document.getElementById('number_of_instalment');
        const pctContainer = document.getElementById('instalmentPercentages');
        const totalSpan = document.getElementById('totalPct');

        function updateTotal() {
            const inputs = pctContainer.querySelectorAll('.pct-input');
            let sum = 0;
            inputs.forEach(inp => sum += parseFloat(inp.value) || 0);
            const roundedSum = Math.round(sum * 10) / 10;
            totalSpan.textContent = roundedSum;
            
            if (roundedSum === 100) {
                totalSpan.parentElement.classList.replace('bg-white', 'bg-success');
                totalSpan.parentElement.classList.replace('text-dark', 'text-white');
            } else {
                totalSpan.parentElement.classList.replace('bg-success', 'bg-white');
                totalSpan.parentElement.classList.replace('text-white', 'text-dark');
            }
        }

        function generatePercentageInputs() {
            const count = parseInt(numberInput.value) || 2;
            const currentInputs = pctContainer.querySelectorAll('.pct-input');
            
            // If count hasn't changed and we have inputs, don't clear (preserves user data on edit)
            if (currentInputs.length === count) return;

            pctContainer.innerHTML = '';
            const equalSplit = Math.floor(100 / count);
            for (let i = 0; i < count; i++) {
                const val = i === count - 1 ? (100 - equalSplit * (count - 1)) : equalSplit;
                const div = document.createElement('div');
                div.className = 'col-6';
                div.innerHTML = `
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text bg-white border-0 text-muted small">P${i + 1}</span>
                    <input type="number" name="list_instalment_percentage[]"
                        class="form-control border-0 pct-input shadow-none bg-white rounded-end" value="${val}" step="0.1" min="1" max="100">
                </div>`;
                pctContainer.appendChild(div);
            }
            pctContainer.querySelectorAll('.pct-input').forEach(inp => inp.addEventListener('input', updateTotal));
            updateTotal();
        }

        function toggleInstallment() {
            const isEnabled = toggleSwitch.checked;
            hiddenStatus.value = isEnabled ? '1' : '0';
            
            installSections.forEach(sec => sec.classList.toggle('d-none', !isEnabled));
            if (fullPaymentNote) fullPaymentNote.classList.toggle('d-none', isEnabled);

            if (isEnabled) {
                numberInput.removeAttribute('disabled');
                if (pctContainer.children.length === 0) { generatePercentageInputs(); }
                updateTotal();
            } else {
                numberInput.setAttribute('disabled', 'disabled');
            }
        }

        if (toggleSwitch) {
            toggleSwitch.addEventListener('change', toggleInstallment);
        }
        
        if (numberInput) {
            numberInput.addEventListener('input', generatePercentageInputs);
        }

        // Attach existing % inputs (edit mode)
        pctContainer.querySelectorAll('.pct-input').forEach(inp => inp.addEventListener('input', updateTotal));

        // Initial Total Calculation
        updateTotal();
    });
</script>