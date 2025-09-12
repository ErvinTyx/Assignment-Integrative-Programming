import React from 'react';
import { PageProps, PaginationProps, Product, Vendor } from "@/types";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import ProductItem from '@/Components/ProductItem';

function Profile({
    vendor,
    products
}: PageProps<{
    vendor: Vendor,
    products:PaginationProps<Product>
}>) {
    return (
        <AuthenticatedLayout>

            <Head title={vendor.store_name + 'Profile Page'} />
            <div className="hero bg-base-200 h-[300px]">
                <div className="hero-content text-center">
                    <div className="max-w-md">
                        <h1 className="text-5xl font-bold">{vendor.store_name}</h1>
                        <p className="py-6">
                            Provident cupiditate voluptatem et in. Quaerat fugiat ut assumenda excepturi exercitationem
                            quasi. In deleniti eaque aut repudiandae et a id nisi.
                        </p>
                    </div>
                </div>
            </div>

            <div className='grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3 p-8'>
                {products.data.map(product => (
                    <ProductItem product={product} key={product.id} />
                ))}
            </div>
        </AuthenticatedLayout>
    );
}

export default Profile;