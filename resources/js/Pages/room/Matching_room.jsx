import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { useState } from 'react';
import axios from 'axios';

export default function MatchingRoom() {
    const [loading, setLoading] = useState(false);
    const [room, setRoom] = useState(null);
    const [error, setError] = useState(null);

    const handleMatch = async () => {
        setLoading(true);
        setError(null);
        try {
            const response = await axios.post('/matching');
            setRoom(response.data.room);
        } catch (error) {
            console.error('Error during matching:', error);
            setError('マッチング中にエラーが発生しました。');
        } finally {
            //マッチングボタンを押したときに実行したい処理
        }
    };

    //const handleMatchCheck =  
    //todo ここにroomstatusを確認する処理を書くのとチェック用の処理をMatchingcontrollerに追加、エンドポイントを追加

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Matching Room
                </h2>
            }
        >
            <Head title="Matching Room" />
            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6 bg-white border-b border-gray-200">
                            <button
                                onClick={handleMatch}
                                className="px-4 py-2 font-semibold text-white bg-blue-500 rounded hover:bg-blue-700"
                                disabled={loading}
                            >
                                {loading ? 'Matching...' : 'Start Matching'}
                            </button>
                            {error && (
                                <div className="mt-4 text-red-500">
                                    {error}
                                </div>
                            )}
                            {room && (
                                <div className="mt-4">
                                    <p>Matched Room: {room.name}</p>
                                    <p>Status: {room.status}</p>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}