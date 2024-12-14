import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { useState } from 'react';
import axios from 'axios';
import { root } from 'postcss';

export default function BattleRoom({ room }) {
    const [loading, setLoading] = useState(false);

    const handleXXXXXXXX = async() => {

    }

    return (
        <>
            <Head title="BattleRoom" />
            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        バトルルームです
                        <div>Room ID: {room}</div>
                    </div>
                </div>
            </div>
        </>
    );
}