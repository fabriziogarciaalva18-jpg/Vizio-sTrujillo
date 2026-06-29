<div class="text-center">
    <h4>Pagar con Plin</h4>
    <div class="my-4">
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{urlencode(config('payments.plin.token')) }}&bgcolor=F5F3EE"
             alt="QR Plin" style="width: 200px; height: 200px; border: 2px solid #eee; padding: 10px;">
    </div>
    <div class="alert alert-retro" style="background: #E0F2FE;">
        <i class="bi bi-phone"></i> <strong>Número Plin:</strong> {{ config('payments.plin.phone') }}
    </div>
    <p class="text-muted">1. Abre Plin<br>2. Escanea el código QR<br>3. Ingresa el monto: <strong>S/. {{ number_format($total , 2) }}</strong><br>4. Coloca como referencia: <strong>TU_NOMBRE</strong><br>5. Confirma el pago</p>
</div>
