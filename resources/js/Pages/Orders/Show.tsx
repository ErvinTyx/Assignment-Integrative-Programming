import { Head, Link, useForm } from "@inertiajs/react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import CurrencyFormatter from "@/Components/CurrencyFormatter";
import { PageProps, Order } from "@/types";

function Show({ order }: PageProps<{ order: Order }>) {
    const { post, processing } = useForm();

    const handleProceed = () => {
        post(route("orders.proceed", order.id));
    };

    const handleCancel = () => {
        post(route("orders.cancel", order.id));
    };

    return (
        <AuthenticatedLayout>
            <Head title={`Order #${order.id}`} />

            <div className="max-w-3xl mx-auto py-8 px-4">
                <h1 className="text-3xl font-bold mb-6">Order Details</h1>

                {/* Order summary */}
                <div className="bg-white dark:bg-gray-800 rounded-md p-6 mb-6 shadow">
                    <div className="flex justify-between mb-3">
                        <span className="text-gray-400">Order Number</span>
                        <span>{order.id}</span>
                    </div>

                    <div className="flex justify-between mb-3">
                        <span className="text-gray-400">Status</span>
                        <span className="font-semibold capitalize">{order.status}</span>
                    </div>

                    <div className="flex justify-between mb-3">
                        <span className="text-gray-400">Created At</span>
                        <span>{order.created_at}</span>
                    </div>

                    <div className="flex justify-between mb-3">
                        <span className="text-gray-400">Seller</span>
                        <span>{order.vendorUser?.store_name ?? "Unknown Seller"}</span>
                    </div>

                    <div className="flex justify-between font-bold text-lg">
                        <span>Total</span>
                        <span>
                            <CurrencyFormatter amount={order.total_price} />
                        </span>
                    </div>
                </div>

                {/* Items */}
                <div className="bg-white dark:bg-gray-800 rounded-md p-6 shadow">
                    <h2 className="text-2xl font-semibold mb-4">Items</h2>

                    {order.orderItems?.length ? (
                        <div className="space-y-4">
                            {order.orderItems.map((item) => (
                                <div key={item.id} className="border-b pb-4 last:border-none">
                                    <div className="flex justify-between">
                                        <span className="font-medium">{item.product?.title ?? "Unknown Product"}</span>
                                        <CurrencyFormatter amount={item.price} />
                                    </div>
                                    <div className="text-sm text-gray-500">Quantity: {item.quantity}</div>
                                    {item.variation_type_option_ids?.length > 0 && (
                                        <div className="text-sm text-gray-400">
                                            Variations: {item.variation_type_option_ids.join(", ")}
                                        </div>
                                    )}
                                </div>
                            ))}
                        </div>
                    ) : (
                        <p className="text-gray-500">No items found in this order.</p>
                    )}
                </div>

                {/* Actions */}
                <div className="flex justify-end mt-6 gap-3">
                    <Link href={route("dashboard")} className="btn">
                        Continue Shopping
                    </Link>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

export default Show;
