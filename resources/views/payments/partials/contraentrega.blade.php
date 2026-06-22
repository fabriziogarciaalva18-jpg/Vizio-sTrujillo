<div class="text-center">
    <i class="bi bi-cash-stack" style="font-size: 4rem; color: #166534;"></i>
    <h4 class="mt-3">Pago Contra Entrega</h4>
    <div class="alert alert-retro mt-4" style="background: #DCFCE7;">
        <i class="bi bi-check-circle-fill"></i> ¡No necesitas pagar ahora!
    </div>
    <p>Pagarás en efectivo al momento de recibir tu pedido.</p>
    <div class="alert alert-retro mt-3">
        <strong>Monto a pagar:</strong> S/. {{ number_format($order->total / 100, 2) }}
    </div>
</div>