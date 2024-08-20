package bitrix

import (
	"github.com/google/uuid"
)

type Solution struct {
	id       uuid.UUID
	nocoID   int64
	bitrixID string
	// Конкатинация id задачи с типом и статусом (777 - Без компенсации - согласился).
	summary string
	// Компенсация в ДС.
	cash string
	// Компенсация в Бонусных баллах.
	bonus string
	// Статус или Результат предложенного решения (Киент согласился например).
	result Result
	// Тип (например "Без компенсации").
	_type Type
	// Фамилия Имя операциониста.
	operator string
	// Ремонтные работы (СК).
	renovationWork string
	// Штраф.
	penalty string
}
