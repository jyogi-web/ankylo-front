import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';
import { useState } from 'react';

export default function Battle() {
    const { data, setData, post, processing, errors } = useForm({
        room_id: '',
    });

    const handleCreateRoom = async () => {
        post('/room/create');
    };

    const handleJoinBattle = async () => {
        post('/room/join', {
            preserveScroll: true,
            onSuccess: () => console.log('Joined room successfully'),
            onError: () => console.error('Error during joining room'),
        });
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Battle
                </h2>
            }
        >
            <Head title="Battle" />
            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6 bg-white border-b border-gray-200">
                            <button
                                onClick={handleCreateRoom}
                                className="px-4 py-2 font-semibold text-white bg-blue-500 rounded hover:bg-blue-700"
                                disabled={processing}
                            >
                                Create Room
                            </button>
                        </div>
                        <div className="p-6 bg-white border-b border-gray-200">
                            <input
                                type="text"
                                value={data.room_id}
                                onChange={(e) => setData('room_id', e.target.value)}
                                placeholder="Enter Room ID"
                                className="px-4 py-2 border rounded"
                            />
                            <button
                                onClick={handleJoinBattle}
                                className="px-4 py-2 ml-2 font-semibold text-white bg-blue-500 rounded hover:bg-blue-700"
                                disabled={processing}
                            >
                                Join Room
                            </button>
                            {errors.room_id && (
                                <div className="mt-4 text-red-500">
                                    <p>{errors.room_id}</p>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}