<div>
    <h4>Transferencia Bancaria</h4>

    <div class="mt-4">
        <div class="alert alert-retro" style="background: #FEF3C7;">
            <i class="bi bi-bank"></i> <strong>Banco BCP</strong>
            <hr>
            <p><strong>Número de Cuenta:</strong> {{ config('payments.bank.bcp.account_number') }}</p>
            <p><strong>CCI:</strong> {{ config('payments.bank.bcp.cci') }}</p>
        </div>

        <div class="alert alert-retro" style="background: #FEF3C7;">
            <i class="bi bi-bank"></i> <strong>Banco Interbank</strong>
            <hr>
            <p><strong>Número de Cuenta:</strong> {{ config('payments.bank.interbank.account_number') }}</p>
            <p><strong>CCI:</strong> {{ config('payments.bank.interbank.cci') }}</p>
        </div>

        <div class="alert alert-retro mt-3">
            <i class="bi bi-info-circle"></i> <strong>Datos importantes:</strong>
            <ul class="mt-2 mb-0">
                <li>Monto a pagar: <strong>S/. {{ number_format($total, 2) }}</strong></li>
                <li>Referencia: <strong>TU_NOMBRE + PEDIDO</strong></li>
                <li>Luego de transferir, sube el comprobante</li>
            </ul>
        </div>
    </div>
</div>
