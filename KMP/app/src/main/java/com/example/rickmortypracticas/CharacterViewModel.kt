package com.example.rickmortypracticas

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.launch

/**
 * Estados posibles de la UI para la pantalla de personajes.
 * Usamos un sealed class para representar cada estado de forma explícita
 * y así evitar combinaciones inválidas de flags (isLoading + error al mismo tiempo, etc).
 */
sealed class CharacterUiState {
    object Loading : CharacterUiState()
    data class Success(val characters: List<Character>) : CharacterUiState()
    data class Error(val message: String) : CharacterUiState()
}

/**
 * ViewModel que actúa de puente entre el módulo compartido y la UI de Compose.
 * Expone un StateFlow para que la pantalla observe los cambios de estado.
 */
class CharacterViewModel : ViewModel() {

    private val api = RickMortyApi()

    private val _uiState = MutableStateFlow<CharacterUiState>(CharacterUiState.Loading)
    val uiState: StateFlow<CharacterUiState> = _uiState

    init {
        loadCharacters()
    }

    fun loadCharacters() {
        viewModelScope.launch {
            _uiState.value = CharacterUiState.Loading
            try {
                val characters = api.getCharacters()
                _uiState.value = CharacterUiState.Success(characters)
            } catch (e: Exception) {
                _uiState.value = CharacterUiState.Error(
                    message = e.message ?: "Error desconocido al cargar los personajes"
                )
            }
        }
    }
}
