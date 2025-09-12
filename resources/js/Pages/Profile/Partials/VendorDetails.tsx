import PrimaryButton from "@/Components/PrimaryButton";
import { useForm, usePage } from "@inertiajs/react";
import React, { FormEventHandler, useRef, useState } from "react";
import SecondaryButton from "@/Components/SecondaryButton";
import Modal from "@/Components/Modal";
import InputLabel from "@/Components/InputLabel";
import TextInput from "@/Components/TextInput";
import InputError from "@/Components/InputError";


export default function VendorDetails(
    { className = '', }: { className?: string; }
) {
    const [showBecomeVendorComfirmation, setShowBecomeVendorComfirmation] = useState(false);
    const [successMessage, setSuccessMessage] = useState('');
    const user = usePage().props.auth.user;
    const token = usePage().props.csrf_token;

    const {
        data,
        setData,
        errors,
        post,
        processing,
        recentlySuccessful,
    } = useForm({
        store_name: user.vendor?.store_name || user.name.toLowerCase().replace(/\s+/g, '-'),
        store_address: user.vendor?.store_address
    });

    const onStoreNameChange = (ev: React.ChangeEvent<HTMLInputElement>) => {
        setData('store_name', ev.target.value.toLowerCase().replace(/\s+/g, '-'));
        setSuccessMessage('');
    }

    const becomeVendor: FormEventHandler = (ev) => {
        ev.preventDefault();

        post(route('vendor.store'), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
                setSuccessMessage("You can now create and publish products.");
            },
            onError: (errors) => {
                console.log(errors);
            },
        })
    }

    const updateVendor: FormEventHandler = (ev) => {
        ev.preventDefault();

        post(route('vendor.store'), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
                setSuccessMessage("Your details were updated");
            },
            onError: (errors) => {
                console.log(errors);
            },
        })
    }

    const closeModal = () => {
        setShowBecomeVendorComfirmation(false);
    }



    return (
        <section className={className}>
            {recentlySuccessful && (
            <div className="toast toast-top toast-end">
                    <div className="alert alert-success">
                        <span>{successMessage}</span>
                    </div>
                </div>
            )}

            <header>
                <h2 className="flex justify-between mb-8 text-lg font-medium text-gray-900 dark:text-gray-100">
                    Vendor Details
                    {user.vendor?.status === 'pending' && (
                        <span className={'badge badge-warning'}>
                            {user.vendor.status_label}
                        </span>
                    )}
                    {user.vendor?.status === 'rejected' && (
                        <span className={'badge badge-error'}>
                            {user.vendor.status_label}
                        </span>
                    )}
                    {user.vendor?.status === 'approved' && (
                        <span className={'badge badge-success'}>
                            {user.vendor.status_label}
                        </span>
                    )}
                </h2>
            </header>
            <div>
                {!user.vendor && (
                    <PrimaryButton disabled={processing} onClick={ev => setShowBecomeVendorComfirmation(true)}>
                        Become a Vendor
                    </PrimaryButton>
                )}
                {user.vendor &&(
                    <>
                    <form onSubmit={updateVendor}>
                        <div className="mb-4">
                            <InputLabel htmlFor="name" value="Store Name"/>

                            <TextInput
                            id="name"
                            className="mt-1 block w-full"
                            value={data.store_name}
                            onChange={onStoreNameChange}
                            required
                            isFocused
                            autoComplete="name"
                            />

                            <InputError message={errors.store_name} className="mt-2"/>
                            <textarea
                             className="textarea textarea-bordered w-full mt-2"
                             value={data.store_address}
                             onChange={(ev) => setData('store_address', ev.target.value)}
                             placeholder="Enter Your Store Address"
                            ></textarea>
                            <InputError message={errors.store_address} className="mt-2"/>

                        </div>
                        <div className="flex items-center gap-4">
                            <PrimaryButton disabled={processing}>Update</PrimaryButton>
                        </div>
                    </form>
                    {/* <form action={route('stripe.connect')} method="post" className="my-8">
                        <input type="hidden" name="_token" value={token} />
                        {user.stripe_account_active && (
                            <div className={'text-center text-gray-600 my-4 text-sm'}>
                                You are successfully connected to Stripe.
                            </div>
                        )}
                        <button className="btn btn-primary w-full " disabled={user.stripe_account_active}>Connect to Stripe</button>
                    </form> */}
                    </>
                    
                )}
            </div>
            <Modal show={showBecomeVendorComfirmation} onClose={closeModal}>
                <form onSubmit={becomeVendor} className="p-8">
                    <h2 className="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Are you sure you want to become a vendor?
                    </h2>

                    <div className="mt-6 flex justify-end">
                        <SecondaryButton onClick={closeModal}>Cancel</SecondaryButton>

                        <PrimaryButton className="ms-3" disabled={processing}>
                            Confirm
                        </PrimaryButton>
                    </div>
                </form>
            </Modal>
        </section>
    );
}