package com.example.rickmortypracticas

import io.ktor.client.HttpClient
import io.ktor.client.call.body
import io.ktor.client.plugins.contentnegotiation.ContentNegotiation
import io.ktor.client.request.get
import io.ktor.serialization.kotlinx.json.json
import kotlinx.serialization.json.Json

/**
 * Cliente HTTP que encapsula todas las llamadas a la API de Rick & Morty.
 *
 * Usamos Ktor como motor HTTP porque es la opción recomendada en KMP:
 * permite compartir el código de red entre Android e iOS usando
 * motores específicos de cada plataforma (OkHttp / Darwin).
 */
class RickMortyApi {

    private val client = HttpClient {
        install(ContentNegotiation) {
            json(Json {
                // Ignoramos campos del JSON que no mapeamos en nuestras data classes
                ignoreUnknownKeys = true
                isLenient = true
            })
        }
    }

    /**
     * Obtiene la primera página de personajes (20 por defecto).
     * Es una función suspend → debe llamarse desde una coroutine.
     */
    suspend fun getCharacters(): List<Character> {
        return client
            .get("https://rickandmortyapi.com/api/character")
            .body<CharacterResponse>()
            .results
    }
}
