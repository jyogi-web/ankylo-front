import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import axios from 'axios';
import React, { useState, useEffect } from 'react';
import SikakuCard from '@/Components/SikakuCard';

export default function Home({ user, hoursRemaining }) {
    const [availablePackDraws, setAvailablePackDraws] = useState(user.available_pack_draws);
    const [reward, setReward] = useState(null);  // reward 状態を追加
    const [errorMessage, setErrorMessage] = useState(null);

    const canDraw = availablePackDraws > 0;

    const handleDraw = async () => {
        if (!canDraw) {
            return alert("ガチャを引ける回数がありません！");
        }

        try {
            const response = await axios.post('/draw-gacha');

            // ガチャ結果と更新された回数を受け取る
            const { cards, available_pack_draws } = response.data;

            // 引いたカードの情報を表示
            console.log(cards);

            // 残り回数の更新
            setAvailablePackDraws(available_pack_draws);

            // ガチャ結果を更新
            setReward(cards);

            setErrorMessage(null); // エラーメッセージをリセット

        } catch (error) {
            console.error(error);

            // エラーメッセージをセット
            const message = error.response?.data?.message || 'ガチャに失敗しました！';
            setErrorMessage(message);
        }
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Home
                </h2>
            }
        >
            <Head title="Home" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg p-6">
                        <h3 className="text-lg font-bold mb-4">拡張パック 最強の資格</h3>
                        <p>引ける回数: {availablePackDraws}</p>
                        <button
                            onClick={handleDraw}
                            className="mt-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700"
                        >
                            開封する
                        </button>

                        {errorMessage && (
                            <div className="mt-4 text-red-500">
                                <p>{errorMessage}</p>
                            </div>
                        )}

                        {!canDraw && (
                            <p className="text-gray-500 mt-4">
                                次の開封まであと {hoursRemaining} 時間
                            </p>
                        )}
                    </div>
                </div>
            </div>

            {reward && (  // reward が存在する場合のみ表示
                <div className="mt-8 text-center">  {/* 中央寄せ */}
                    <h4 className="text-green-500 font-bold text-2xl mb-4">ガチャ結果:</h4>
                    <div className="flex flex-wrap gap-6 justify-center">  {/* gap-6 と justify-center で中央に配置 */}
                        {reward.map((card) => (  // reward 配列を反復処理
                            <SikakuCard
                                key={card.id} // card.id をキーとして使用
                                rank={card.rank ?? "No Card Name"}
                                power={card.power ?? "No Power"}
                                title={card.name ?? "No Name"}
                                genre={card.type ?? "No Genre"}
                                weakGenre={card.type ?? "No Genre"}
                                description={card.description ?? "No Description"}
                            />
                        ))}
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
