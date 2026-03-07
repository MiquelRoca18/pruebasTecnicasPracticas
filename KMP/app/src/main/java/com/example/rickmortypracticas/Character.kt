package com.example.rickmortypracticas

import kotlinx.serialization.Serializable

/**
 * Modelo que representa la respuesta paginada de la API de Rick & Morty.
 */
@Serializable
data class CharacterResponse(
    val info: PageInfo,
    val results: List<Character>
)

/**
 * Información de paginación devuelta por la API.
 */
@Serializable
data class PageInfo(
    val count: Int,
    val pages: Int,
    val next: String?,
    val prev: String?
)

/**
 * Modelo de personaje con solo los campos que necesitamos.
 * Solo mapeamos id, name y status para no cargar datos innecesarios.
 */
@Serializable
data class Character(
    val id: Int,
    val name: String,
    val status: String
)
