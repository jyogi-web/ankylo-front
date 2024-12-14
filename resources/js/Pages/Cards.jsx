import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import React, { useEffect, useState } from 'react';

export default function Cards() {
    const [user_cards, setUserCards] = useState([]);//データを保存する状態

    //データを取得する関数
    useEffect(() => {
        fetch('http://localhost/api/cards')//Laravelのエンドポイント
            .then((response) => response.json())
            .then((data) => setUserCards(data))//データを状態に保存
            .catch((error) => console.error('Error fetching data:', error));
    }, []);

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Cards
                </h2>
            }
        >
            <Head title="Cards" />
            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    {user_cards.map((user_card) => (
                        <div
                            key={user_card.id}
                            className="overflow-hidden bg-white shadow-sm sm:rounded-lg p-4 mb-4"
                        >
                            <h3 className="text-lg font-bold">
                                {user_card.card?.name ?? "No Card Name"}
                            </h3>
                            <p>{user_card.card?.description ?? "No Description"}</p>
                        </div>
                    ))}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
