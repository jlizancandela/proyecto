<?php

/**
 * This file helps the code editor understand the shortcuts we use for testing.
 * It stops the editor from complaining about missing functions like expect or test.
 */

namespace {

    class PestTest
    {
        /**
         * @param string $exception
         * @param string|null $message
         * @return self
         */
        public function throws($exception, $message = null)
        {
            return $this;
        }
    }

    class PestExpectation
    {
        /**
         * @param mixed $value
         * @return self
         */
        public function toBe($value)
        {
            return $this;
        }

        /**
         * @return self
         */
        public function toBeTrue()
        {
            return $this;
        }

        /**
         * @return self
         */
        public function toBeNull()
        {
            return $this;
        }

        /**
         * @param int $count
         * @return self
         */
        public function toHaveCount($count)
        {
            return $this;
        }
    }

    /**
     * @param string $description
     * @param callable $closure
     * @return PestTest
     */
    function test($description = null, $closure = null)
    {
        return new PestTest();
    }

    /**
     * @param string $description
     * @param callable $closure
     * @return PestTest
     */
    function it($description = null, $closure = null)
    {
        return new PestTest();
    }

    /**
     * @param mixed $value
     * @return PestExpectation
     */
    function expect($value)
    {
        return new PestExpectation();
    }

    /**
     * @param callable $closure
     * @return void
     */
    function beforeEach($closure) {}

    /**
     * @param callable $closure
     * @return void
     */
    function afterEach($closure) {}
}
