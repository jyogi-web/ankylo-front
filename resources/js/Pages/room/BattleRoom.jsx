import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import axios from 'axios';

export default function BattleRoom({ room, initialUsers }) {
    const [users, setUsers] = useState(initialUsers || []);

    useEffect(() => {
        const interval = setInterval(() => {
            axios.get(`/api/room/${room.id}/users`)
                .then(response => {
                    setUsers(response.data);
                })
                .catch(error => {
                    console.error('Error fetching users:', error);
                });
        }, 1000); // 1秒ごとに更新

        return () => clearInterval(interval);
    }, [room.id]);

    return (
        <>
            <Head title="BattleRoom" />
            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6 bg-white border-b border-gray-200">
                            <h3>Room ID: {room.id}</h3>
                            <h4>Users in this room:</h4>
                            <ul>
                                {users.length > 0 ? (
                                    users.map((user) => (
                                        <li key={user.id}>{user.name}</li>
                                    ))
                                ) : (
                                    <li>No users in this room</li>
                                )}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}