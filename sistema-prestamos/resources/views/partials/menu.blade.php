<aside class="menu">

        @if (request()->routeIs('dashboard'))
        <div class="logo">
            <i class="bi bi-wallet2"></i>
        </div>
        @endif

        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'activo' : '' }}" @if (request()->routeIs('dashboard')) aria-current="page" @endif title="Inicio">
            <i class="bi bi-grid-1x2-fill"></i>
        </a>

        @can('gestionar-clientes')
        <a href="{{ route('clientes.index') }}" class="{{ request()->routeIs('clientes.*', 'garantes.*') ? 'activo' : '' }}" @if (request()->routeIs('clientes.*', 'garantes.*')) aria-current="page" @endif title="Clientes y garantes">
    <i class="bi bi-people"></i>
</a>
        @endcan

        <a href="{{ route('tipos-prestamo.index') }}" class="{{ request()->routeIs('tipos-prestamo.*') ? 'activo' : '' }}" @if (request()->routeIs('tipos-prestamo.*')) aria-current="page" @endif
   title="Tipos de préstamo">
    <i class="bi bi-tags"></i>
</a>

        <a href="#" title="Apertura de préstamos">
            <i class="bi bi-cash-stack"></i>
        </a>

        <a href="#" title="Recibos de ingreso">
            <i class="bi bi-receipt"></i>
        </a>

        <a href="#" title="Renovaciones">
            <i class="bi bi-arrow-repeat"></i>
        </a>

        @if (request()->routeIs('dashboard'))
        <a href="#" title="Consultas">
            <i class="bi bi-search"></i>
        </a>

        <form action="{{ route('logout') }}" method="POST" style="margin-top: auto;">
    @csrf

    <button type="submit"
            title="Cerrar sesión"
            style="background: transparent; border: none; color: #c9c3bd;
                   font-size: 22px; width: 48px; height: 48px; cursor: pointer;">
        <i class="bi bi-box-arrow-right"></i>
    </button>
</form>
        @endif

    </aside>
