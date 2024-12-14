import React, { useEffect, useState } from 'react';
import axios from 'axios';

const DeckCardList = ({ deckId, roomId, userId, turn, onCardSelected }) => {
    const [cards, setCards] = useState([]);

    useEffect(() => {
        axios.get(`/api/decks/${deckId}/cards`)
            .then(response => {
                setCards(response.data);
            })
            .catch(error => {
                console.error('Error fetching cards:', error);
            });
    }, [deckId]);

    const handleCardClick = (card) => {
        console.log('select-card');
        axios.post(`/api/room/${roomId}/select-card`, {
            user_id: userId,
            card_id: card.id,
            turn: turn
        })
        .then(response => {
            console.log('judge');
            onCardSelected(card);
        })
        .catch(error => {
            console.error('Error selecting card:', error);
        });
    };

    return (
        <div>
            <h3 className="text-lg font-bold mb-4">Deck ID: {deckId}</h3>
            <ul className="space-y-2">
                {cards.length > 0 ? (
                    cards.map((card) => (
                        <li
                            key={card.id}
                            onClick={() => handleCardClick(card)}
                            className="p-4 bg-gray-100 rounded-lg shadow-md cursor-pointer hover:bg-gray-200"
                        >
                            <div className="font-semibold">{card.name}</div>
                            <div className="text-sm text-gray-600">{card.type}</div>
                            <div className="text-sm text-gray-600">偏差値 {card.power}</div>
                        </li>
                    ))
                ) : (
                    <li className="text-gray-500">No cards in this deck</li>
                )}
            </ul>
        </div>
    );
};

export default DeckCardList;
