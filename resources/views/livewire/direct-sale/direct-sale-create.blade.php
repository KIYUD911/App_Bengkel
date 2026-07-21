<div>
<div class="page-header">
    <div>
        <h2 class="page-title">Penjualan Langsung</h2>
        <p class="page-subtitle">Proses penjualan sparepart tanpa work order</p>
    </div>
    <a href="{{ route('direct-sales.index') }}" class="btn btn-secondary">← Riwayat Penjualan</a>
</div>

{{-- Success State --}}
@if($showSuccess)
    <div class="card" style="max-width:500px;margin:0 auto;text-align:center;padding:2rem;">
        <div style="font-size:4rem;margin-bottom:1rem;">✅</div>
        <h3 style="color:var(--success);margin-bottom:.5rem;">Penjualan Berhasil!</h3>
        <div style="font-size:1.5rem;font-weight:700;font-family:monospace;color:var(--primary);margin:.75rem 0;">{{ $saleNumber }}</div>
        <p style="color:var(--text-muted);font-size:var(--text-sm);margin-bottom:1.5rem;">Transaksi telah tercatat dan stok telah diperbarui.</p>
        <div class="flex gap-2" style="justify-content:center;">
            <a href="/direct-sales/{{ $saleId }}/receipt" class="btn btn-secondary">🖨️ Cetak Struk</a>
            <button wire:click="newTransaction" class="btn btn-primary">+ Transaksi Baru</button>
        </div>
    </div>
@else

{{-- Error --}}
@if($errorMessage)
    <div class="alert alert-danger">
        {{ $errorMessage }}
        <button wire:click="$set('errorMessage', null)" style="margin-left:auto;background:none;border:none;cursor:pointer;font-size:1.25rem;">×</button>
    </div>
@endif

<div class="grid grid-2" style="align-items:start;">

    {{-- Kiri: Pelanggan + Cari Part --}}
    <div style="display:flex;flex-direction:column;gap:1.25rem;">

        {{-- Tipe Pelanggan --}}
        <div class="card">
            <div class="card-header"><span class="card-title">👤 Informasi Pembeli</span></div>

            {{-- Toggle --}}
            <div style="display:flex;border:1px solid var(--border);border-radius:var(--radius-sm);overflow:hidden;margin-bottom:1rem;">
                <button wire:click="$set('buyerType','walk_in')"
                    style="flex:1;padding:.5rem;font-size:var(--text-sm);font-weight:500;border:none;cursor:pointer;transition:all var(--transition);background:{{ $buyerType==='walk_in' ? 'var(--primary)' : 'white' }};color:{{ $buyerType==='walk_in' ? 'white' : 'var(--text-secondary)' }};">
                    👤 Walk-in
                </button>
                <button wire:click="$set('buyerType','registered')"
                    style="flex:1;padding:.5rem;font-size:var(--text-sm);font-weight:500;border:none;cursor:pointer;transition:all var(--transition);background:{{ $buyerType==='registered' ? 'var(--primary)' : 'white' }};color:{{ $buyerType==='registered' ? 'white' : 'var(--text-secondary)' }};">
                    📋 Pelanggan Terdaftar
                </button>
            </div>

            @if($buyerType === 'walk_in')
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Nama Pembeli (opsional)</label>
                    <input type="text" wire:model="walkInName" class="form-input" placeholder="Nama pelanggan walk-in..." />
                </div>
            @else
                <div class="form-group" style="position:relative;margin-bottom:0;">
                    <label class="form-label">Cari Pelanggan Terdaftar</label>
                    <input type="text" wire:model.live.debounce.300ms="customerSearch"
                        class="form-input" placeholder="Ketik nama atau telepon..." autocomplete="off" />
                    @if(count($customerResults) > 0)
                        <div style="position:absolute;top:100%;left:0;right:0;background:white;border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow-md);z-index:50;margin-top:.25rem;">
                            @foreach($customerResults as $c)
                                <div wire:click="selectCustomer({{ $c['id'] }}, '{{ addslashes($c['name']) }}')"
                                    style="padding:.625rem 1rem;cursor:pointer;display:flex;justify-content:space-between;"
                                    onmouseover="this.style.background='var(--surface)'" onmouseout="this.style.background='white'">
                                    <div>
                                        <div style="font-weight:500;font-size:var(--text-sm);">{{ $c['name'] }}</div>
                                        <div style="font-size:var(--text-xs);color:var(--text-muted);">{{ $c['phone'] }}</div>
                                    </div>
                                    @if($c['is_vip'])<span class="badge" style="background:#FDF4FF;color:#7E22CE;">⭐ VIP</span>@endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                    @if($selectedCustomerName)
                        <div class="alert alert-success" style="margin-top:.5rem;padding:.5rem .75rem;">
                            ✅ <strong>{{ $selectedCustomerName }}</strong>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        {{-- Cari Sparepart --}}
        <div class="card">
            <div class="card-header"><span class="card-title">🔍 Tambah ke Keranjang</span></div>

            <div class="form-group" style="position:relative;">
                <label class="form-label">Cari Sparepart</label>
                <input type="text" wire:model.live.debounce.300ms="partSearch"
                    class="form-input" placeholder="Nama atau kode part..." autocomplete="off" />
                @if(count($partResults) > 0)
                    <div style="position:absolute;top:100%;left:0;right:0;background:white;border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow-md);z-index:50;margin-top:.25rem;max-height:220px;overflow-y:auto;">
                        @foreach($partResults as $p)
                            <div wire:click="selectPart({{ $p['id'] }})"
                                style="padding:.625rem 1rem;cursor:pointer;display:flex;justify-content:space-between;border-bottom:1px solid var(--border);"
                                onmouseover="this.style.background='var(--surface)'" onmouseout="this.style.background='white'">
                                <div>
                                    <div style="font-weight:500;font-size:var(--text-sm);">{{ $p['name'] }}</div>
                                    <div style="font-size:var(--text-xs);color:var(--text-muted);">Stok: {{ $p['quantity_available'] }} {{ $p['unit'] }}</div>
                                </div>
                                <div style="font-weight:600;font-size:var(--text-sm);">Rp {{ number_format($p['selling_price'], 0, ',', '.') }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            @if($selectedPart)
                <div style="background:var(--primary-light);border:1px solid #BFDBFE;border-radius:var(--radius-sm);padding:.75rem;margin-bottom:.75rem;">
                    <div style="font-weight:600;font-size:var(--text-sm);">{{ $selectedPart['name'] }}</div>
                    <div style="font-size:var(--text-xs);color:var(--text-muted);">Stok: {{ $selectedPart['quantity_available'] }} · Rp {{ number_format($selectedPart['selling_price'], 0, ',', '.') }}</div>
                </div>
                <div class="flex gap-2" style="align-items:flex-end;">
                    <div class="form-group" style="width:100px;margin-bottom:0;">
                        <label class="form-label">Qty</label>
                        <input type="number" wire:model.live="cartItemQty" class="form-input" min="1" max="{{ $selectedPart['quantity_available'] }}" />
                    </div>
                    <button wire:click="addToCart" class="btn btn-primary">+ Tambah</button>
                    <button wire:click="$set('selectedPart', null); $set('partSearch', '')" class="btn btn-secondary">Batal</button>
                </div>
            @endif
        </div>

        {{-- Metode Pembayaran --}}
        <div class="card">
            <div class="card-header"><span class="card-title">💳 Pembayaran</span></div>
            <div style="display:flex;gap:.75rem;margin-bottom:.75rem;">
                @foreach(['tunai'=>'💵 Tunai','transfer'=>'🏦 Transfer','kartu'=>'💳 Kartu'] as $val => $lab)
                    <label style="flex:1;display:flex;flex-direction:column;align-items:center;padding:.625rem;border:2px solid {{ $paymentMethod===$val ? 'var(--primary)' : 'var(--border)' }};border-radius:var(--radius-sm);cursor:pointer;background:{{ $paymentMethod===$val ? 'var(--primary-light)' : 'white' }};"
                        wire:click="$set('paymentMethod','{{ $val }}')">
                        <span style="font-size:1.1rem;">{{ explode(' ',$lab)[0] }}</span>
                        <span style="font-size:var(--text-xs);font-weight:500;margin-top:.2rem;">{{ explode(' ',$lab)[1] }}</span>
                    </label>
                @endforeach
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Catatan (opsional)</label>
                <input type="text" wire:model="notes" class="form-input" placeholder="Catatan tambahan..." />
            </div>
        </div>
    </div>

    {{-- Kanan: Keranjang --}}
    <div class="card" style="position:sticky;top:80px;">
        <div class="card-header">
            <span class="card-title">🛒 Keranjang Belanja</span>
            <span class="badge badge-primary">{{ count($cartItems) }} item</span>
        </div>

        @if(empty($cartItems))
            <div class="empty-state" style="padding:2rem;">
                <div class="empty-state-icon">🛒</div>
                <div class="empty-state-text">Keranjang kosong. Cari dan tambahkan sparepart.</div>
            </div>
        @else
            <div class="table-wrap" style="margin-bottom:1rem;">
                <table>
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th style="text-align:center;">Qty</th>
                            <th style="text-align:right;">Harga</th>
                            <th style="text-align:right;">Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cartItems as $i => $item)
                            <tr>
                                <td style="font-weight:500;font-size:var(--text-sm);">{{ $item['name'] }}</td>
                                <td style="text-align:center;">{{ $item['qty'] }} {{ $item['unit'] }}</td>
                                <td style="text-align:right;font-size:var(--text-sm);">Rp {{ number_format($item['unit_price'], 0, ',', '.') }}</td>
                                <td style="text-align:right;font-weight:600;">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                                <td><button wire:click="removeFromCart({{ $i }})" class="btn btn-sm btn-danger">×</button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="display:flex;justify-content:space-between;align-items:center;padding:1rem;background:var(--primary-light);border-radius:var(--radius-sm);margin-bottom:1rem;">
                <span style="font-weight:600;">TOTAL</span>
                <span style="font-size:var(--text-xl);font-weight:700;color:var(--primary);">Rp {{ number_format($this->grandTotal, 0, ',', '.') }}</span>
            </div>

            <button wire:click="processSale" class="btn btn-success" style="width:100%;font-size:var(--text-base);" wire:loading.attr="disabled">
                <span wire:loading.remove>✅ Proses Penjualan</span>
                <span wire:loading>Memproses...</span>
            </button>
        @endif
    </div>
</div>
@endif
</div>
