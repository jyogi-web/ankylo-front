import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import axios from 'axios';
import React, { useState, useEffect } from 'react';

export default function Home({ user, hoursRemaining }) {
    const [availablePackDraws, setAvailablePackDraws] = useState(user.available_pack_draws);
    const [reward, setReward] = useState(null);
    const [errorMessage, setErrorMessage] = useState(null);

    const canDraw = availablePackDraws > 0;

    const handleDraw = async () => {
        if (!canDraw) {
            return alert("ガチャを引ける回数がありません！");
        }

        try {
            // APIにリクエストを送る
            const response = await axios.post('/draw-gacha');

            // ガチャ結果と更新された回数を受け取る
            const { reward, available_pack_draws } = response.data;

            // ガチャ結果を更新
            setReward(reward);
            setAvailablePackDraws(available_pack_draws); // 残り回数を更新

        } catch (error) {
            // エラーハンドリング
            if (error.response && error.response.data.message) {
                setErrorMessage(error.response.data.message);
            } else {
                setErrorMessage('ガチャを引く際にエラーが発生しました。');
            }
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
                        <h3 className="text-lg font-bold mb-4">ガチャ機能</h3>
                        <p>引ける回数: {availablePackDraws}</p>

                        {reward && (
                            <div className="mt-4">
                                <h4 className="text-green-500 font-bold">ガチャ結果:</h4>
                                <p>{reward.name} ({reward.rarity})</p>
                            </div>
                        )}

                        {errorMessage && (
                            <div className="mt-4 text-red-500">
                                <p>{errorMessage}</p>
                            </div>
                        )}

                        {canDraw ? (
                            <button
                                onClick={handleDraw}
                                className="mt-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700"
                            >
                                ガチャを引く
                            </button>
                        ) : (
                            <p className="text-gray-500 mt-4">
                                次のガチャまであと {hoursRemaining} 時間
                            </p>
                        )}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
