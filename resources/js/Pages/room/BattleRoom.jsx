import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head,Link } from '@inertiajs/react';
import DeckCardList from '../../Components/DeckCardList';
import { useState, useEffect } from 'react';
import axios from 'axios';

export default function BattleRoom({ room, initialUsers, user_id }) {
    const [users, setUsers] = useState(initialUsers || []);
    const [selectedCard, setSelectedCard] = useState(null);
    const [turn, setTurn] = useState(room.turn);
    const [winner, setWinner] = useState(null);
    const [cardSelected, setCardSelected] = useState(false);
    const [result, setResult] = useState(false); // 試合結果表示の状態
    const [winnerStats, setWinnerStats] = useState(null); // 勝者情報
    const [totalPowerDifference, setTotalPowerDifference] = useState(0); // パワー差合計
    const [overallWinner, setOverallWinner] = useState(null); // 試合全体の勝者

    useEffect(() => {
        console.log('Winner updated:', winner);
        setCardSelected(false); // ターンが更新されたらカード選択をリセット
        setSelectedCard(null);

        // 5ターン終了後に結果を表示
        if (turn >= 6) {
            setResult(true);
        }
    }, [turn]);

    useEffect(() => {
        const interval = setInterval(() => {
            console.log('setInterval');
            axios.get(`/api/room/${room.id}/check-all-selected`)
                .then(response => {
                    setWinner(response.data.winner);
                    setTurn(response.data.turn);
                    if (response.data.allSelected) {
                        // 全員選択済みなら判定ロジックへ
                        console.log('allSelected');
                        handleJudge(response.data.created_by);
                    }
                })
                .catch(error => console.error(error));
        }, 1000); // 1秒間隔でチェック

        return () => clearInterval(interval);
    }, [room.id]);

    useEffect(() => {
        if (result) {
            console.log('Fetching result...');
            axios.get(`/api/room/${room.id}/result`)
                .then(response => {
                    const { winner_stats } = response.data;
                    const winnerId = Object.keys(winner_stats)[0]; // 勝者のユーザーID（1人のみ想定）
                    setOverallWinner(winnerId);
                    setWinnerStats(winner_stats[winnerId]);
                    setTotalPowerDifference(winner_stats[winnerId]?.total_power_difference || 0);
                })
                .catch(error => {
                    console.error('Error fetching turn history:', error);
                });
        }
    }, [result]);

    const handleJudge = (created_by) => {
        console.log('judge');
        console.log('created', created_by, user_id);
        setCardSelected(false); // ターンが更新されたらカード選択をリセット
        setSelectedCard(null); // 選択したカードもリセット

        if (created_by === user_id) {
            axios.post(`/api/room/${room.id}/judge`, { turn })
                .then(response => {
                    if (response.data.winner) {
                        console.log('Judge response:', response.data);
                    }
                })
                .catch(error => {
                    console.error('Error judging turn:', error);
                });
        }
    };

    const handleCardSelected = (card) => {
        setSelectedCard(card.id);
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
                            <h4>UserId: {user_id}</h4>
                            <h4>Winner: {winner}</h4>
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

                            {/* 試合結果の表示 */}
                            {result && winnerStats && (
                                <div className="mt-6">
                                    <h4>試合結果</h4>
                                    <p>勝者: {overallWinner}</p>
                                    <p>パワー差合計: {totalPowerDifference}</p>
                                    {/* パワー差合計が50以上の場合の追加表示 */}
                                    {totalPowerDifference >= 50 && (
                                        <>                                        <p className="text-green-500 font-bold">マウントの取りすぎ！</p>
                                            <p className='text-red-500 font-bold'>人間失格！！！！</p>
                                        </>
                                    )}
                                    <p>勝利回数: {winnerStats.win_count}</p>
                                </div>
                                //ここに戻るボタン
                            )}
                            {/* aaaaaaaa */}
                            {result && (<div><Link href='/home' >戻る</Link></div>)}
                        </div>
                    </div>
                </div>

                {/* カード表示は結果表示時に非表示 */}
                {!cardSelected && !result && (
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
