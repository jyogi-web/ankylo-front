import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import DeckCardList from '../../Components/DeckCardList';
import { useState, useEffect } from 'react';
import axios from 'axios';

export default function BattleRoom({ room, initialUsers, user_id }) {
    const [users, setUsers] = useState(initialUsers || []);
    const [selectedCard, setSelectedCard] = useState(null);
    const [turn, setTurn] = useState(room.turn);
    const [winner, setWinner] = useState(null);
    const [cardSelected, setCardSelected] = useState(false);

    useEffect(() => {
        const interval = setInterval(() => {
            console.log("setInterval");
            axios.get(`/api/room/${room.id}/check-all-selected`)
                .then(response => {
                    if (response.data.allSelected) {
                        // 全員選択済みなら判定ロジックへ
                        handleJudge();
                    }
                })
                .catch(error => console.error(error));
        }, 1000); // 1秒間隔でチェック
    
        return () => clearInterval(interval);
    }, [room.id]);
    
    const handleJudge = () => {
        // 自分の選択したカードのデータを含めて送信
        console.log("judge");
        axios.post(`/api/room/${room.id}/judge`, { 
            turn, 
            selectedCard 
        })
        .then(response => {
            if (response.data.winner) {
                setWinner(response.data.winner); // 勝者を更新
                setTurn(turn + 1); // ターンを進める
                setCardSelected(false); // ターンが更新されたらカード選択をリセット
                setSelectedCard(null); // 選択したカードもリセット
            }
        })
        .catch(error => {
            console.error('Error judging turn:', error);
        });
    };
    
    const handleCardSelected = (card) => {
        setSelectedCard(card);
        setCardSelected(true); // カードが選択されたことを記録
    };

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
                                    <li>更新中</li>
                                )}
                            </ul>
                            <h4>Current Turn: {turn}</h4>
                            {winner && <h4>Winner: {winner}</h4>}
                        </div>
                    </div>
                </div>
                
                {/* 自分のカード表示 */}
                {!cardSelected && (
                    <DeckCardList
                        deckId={user_id}
                        roomId={room.id}
                        userId={user_id}
                        turn={turn}
                        onCardSelected={handleCardSelected}
                    />
                )}
            </div>
        </>
    );
}