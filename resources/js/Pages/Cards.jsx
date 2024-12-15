import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import React, { useEffect, useState } from 'react';
import SikakuCard from '@/Components/SikakuCard';

export default function Cards() {
    const [user_cards, setUserCards] = useState([]);//データを保存する状態

    //データを取得する関数
    useEffect(() => {
        fetch('/api/cards')//Laravelのエンドポイント
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
                <div className="flex flex-wrap gap-6 justify-center">
                    {user_cards.map((user_card) => (
                        <SikakuCard
                            key={user_card.id} // IDをキーとして追加
                            rank={user_card.card?.rank ?? "No Card Name"}
                            power={user_card.card?.power ?? "No Card Name"}
                            title={user_card.card?.name ?? "No Card Name"}
                            genre={user_card.card?.type ?? "No Card Name"}
                            weakGenre={user_card.card?.type ?? "No Card Name"}
                            description={user_card.card?.description ?? "No Description"}
                        />
                    ))}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
