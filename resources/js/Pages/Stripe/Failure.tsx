import { Head, Link } from "@inertiajs/react";
import CurrencyFormatter from "@/Components/CurrencyFormatter";
import { XCircleIcon } from "@heroicons/react/24/outline";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { PageProps, Order } from "@/types";

function Failure({ orders }: PageProps<{ orders: Order[] }>) {
    return (
        <AuthenticatedLayout>
            <Head title="Payment Failed" />

            <div className="w-[480px] mx-auto py-8 px-4">
                <div className="flex flex-col gap-2 items-center">
                    <div className="text-6xl text-red-500">
                        <XCircleIcon className="h-20 w-20 text-red-500" />
                    </div>
                    <div className="text-3xl">
                        Payment Failed
                    </div>
                </div>
                <div className="my-6 text-lg text-center">
                    Oops! Something went wrong with your payment.
                    Please try again or contact support if the problem persists.
                </div>

                {orders.map((order) => (
                    <div key={order.id} className="bg-white dark:bg-gray-800 rounded-md p-6 mb-4">
                        <h3 className="text-2xl mb-3 text-red-500">Failed Order Summary</h3>

                        <div className="flex justify-between mb-2 font-bold">
                            <div className="text-gray-700 dark:text-gray-300">
                                Seller
                            </div>
                            <div>
                                <Link href="#" className="hover:underline">
                                    {order.vendorUser.store_name}
                                </Link>
                            </div>
                        </div>

                        <div className="flex justify-between mb-2">
                            <div className="text-gray-400">Order Number</div>
                            <div>{order.id}</div>
                        </div>

                        <div className="flex justify-between mb-3">
                            <div className="text-gray-400">Items</div>
                            <div>{order.orderItems.length}</div>
                        </div>

                        <div className="flex justify-between mb-3">
                            <div className="text-gray-400">Total</div>
                            <div>
                                <CurrencyFormatter amount={order.total_price} />
                            </div>
                        </div>

                        <div className="flex justify-between mt-4">
                            <Link href={route('cart.index')} className="btn btn-primary">
                                Try Again
                            </Link>
                            <Link href={route('dashboard')} className="btn">
                                Continue Shopping
                            </Link>
                        </div>
                    </div>
                ))}
            </div>
        </AuthenticatedLayout>
    );
}

export default Failure;
