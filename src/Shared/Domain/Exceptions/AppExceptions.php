<?php

/**
 * AppExceptions
 *
 * Centralized file for all custom application exceptions.
 * This simplifies exception management and keeps the code clean.
 */

namespace Shared\Domain\Exceptions;

/**
 * Exception thrown when a user is not found or is invalid.
 */
class InvalidUserException extends \RuntimeException {}

/**
 * Exception thrown when an email is already in use or is invalid.
 */
class InvalidEmailException extends \RuntimeException {}

/**
 * Exception thrown when a password does not meet the security requirements.
 */
class InvalidPasswordException extends \RuntimeException {}

/**
 * Exception thrown when user profile data is invalid.
 */
class InvalidUserDataException extends \RuntimeException {}

/**
 * General purpose validation exception.
 */
class InvalidValidation extends \Exception {}

/**
 * Exception thrown when a service is not found.
 */
class ServiceNotFoundException extends \RuntimeException {}

/**
 * Exception thrown when a service operation fails.
 */
class ServiceOperationException extends \RuntimeException {}

/**
 * Exception thrown when service data validation fails.
 */
class ServiceValidationException extends \RuntimeException {}

/**
 * Exception thrown when a booking is not found.
 */
class BookingNotFoundException extends \RuntimeException {}

/**
 * Exception thrown when a booking operation fails.
 */
class BookingOperationException extends \RuntimeException {}

/**
 * Exception thrown when booking data validation fails.
 */
class BookingValidationException extends \RuntimeException {}

/**
 * Exception thrown when a booking conflict is detected.
 */
class BookingConflictException extends \RuntimeException {}

/**
 * Exception thrown when a booking limit is exceeded.
 */
class BookingLimitException extends \RuntimeException {}

/**
 * Exception thrown when a required dependency is missing.
 */
class MissingDependencyException extends \RuntimeException {}
