<flux:card class="space-y-4 sm:space-y-6 p-5 sm:p-6">
    <div>
        <flux:heading size="lg">
            {{ trans('orders.title') }}
        </flux:heading>
        <flux:subheading class="!leading-relaxed">
            {{ trans('orders.description') }}
        </flux:subheading>
    </div>
    <flux:card class="pt-3">
        <flux:table :paginate="$this->orders">
            <flux:table.columns>
                <flux:table.column>
                    {{ trans('orders.table.header.package') }}
                </flux:table.column>
                <flux:table.column>
                    {{ trans('orders.table.header.credits') }}
                </flux:table.column>
                <flux:table.column>
                    {{ trans('orders.table.header.date') }}
                </flux:table.column>
                <flux:table.column align="end">
                    {{ trans('orders.table.header.amount') }}
                </flux:table.column>
                <flux:table.column class="w-24" align="center">
                    {{ trans('orders.table.header.invoice') }}
                </flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->orders as $order)
                    <flux:table.row :key="$order->id">
                        <flux:table.cell>
                            {{ $order->package_name }}
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ trans('orders.table.row.credits', ['tokens' => $order->tokens]) }}
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $order->created_at->format('jS M Y') }}
                        </flux:table.cell>
                        <flux:table.cell variant="strong" align="end">
                            {{ $order->payment?->formatted_amount ?? $order->formatted_price}}
                        </flux:table.cell>
                        <flux:table.cell align="center">
                            @if($order->free)
                                {{ trans('misc.unavailable') }}
                            @else
                                <flux:button
                                    size="sm"
                                    variant="outline"
                                    wire:click="downloadPdf('{{ $order->id }}')"
                                    icon="cloud-download"
                                >
                                    {{ trans('orders.table.row.download') }}
                                </flux:button>
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.row>
        </flux:table>
    </flux:card>
</flux:card>
