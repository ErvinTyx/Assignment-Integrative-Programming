import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head } from "@inertiajs/react";
import { OrderView } from "@/types";
import CurrencyFormatter from "@/Components/CurrencyFormatter";

interface Props {
    order: OrderView;
}

export default function Show({ order }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={`Order #${order.id}`} />

            <div className="container mx-auto px-4 py-8">
                <h1 className="text-2xl font-bold mb-4">Order #{order.id}</h1>

                <div className="mb-6 p-4 border rounded-md bg-base-200">
                    <p><strong>Status:</strong> <span className="badge">{order.status}</span></p>
                    <p><strong>Order Date:</strong> {order.created_at}</p>
                    <p><strong>Total Price:</strong> <CurrencyFormatter amount={order.total_price} /></p>
                </div>

                <div className="mb-6 p-4 border rounded-md bg-base-100">
                    <h2 className="text-lg font-semibold mb-2">Vendor</h2>
                    <p><strong>Store Name:</strong> {order.vendorUser.store_name}</p>
                    <p><strong>Email:</strong> {order.vendorUser.email}</p>
                </div>

                <div className="p-4 border rounded-md bg-base-100">
                    <h2 className="text-lg font-semibold mb-4">Items</h2>
                    <div className="space-y-4">
                        {order.orderItems.map((item) => (
                            <div key={item.id} className="flex items-start gap-4 p-4 border-b">
                                <img src={item.product.image} alt={item.product.title} className="w-20 h-20 object-cover rounded-md" />
                                <div className="flex-1">
                                    <p className="font-bold">{item.product.title}</p>
                                    <p className="text-sm text-gray-500">{item.product.description}</p>
                                    <p className="mt-2">Quantity: {item.quantity}</p>
                                    <p className="text-sm text-primary">Variation IDs: {item.variation_type_option_ids?.join(', ') || 'None'}</p>
                                </div>
                                <div className="font-semibold">
                                    <CurrencyFormatter amount={item.price} />
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
