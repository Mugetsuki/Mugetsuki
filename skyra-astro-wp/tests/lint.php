<?php
/**
 * Parse-checks every PHP file under the given directory (php -l equivalent
 * that also runs on php-wasm). Usage: php tests/lint.php <dir>
 */
$root = $argv[1] ?? '.';
$bad  = 0;
$n    = 0;
$it   = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $root, FilesystemIterator::SKIP_DOTS ) );
foreach ( $it as $file ) {
	$path = (string) $file;
	if ( ! str_ends_with( $path, '.php' ) || str_contains( $path, '/node_modules/' ) ) {
		continue;
	}
	++$n;
	try {
		token_get_all( (string) file_get_contents( $path ), TOKEN_PARSE );
	} catch ( ParseError $e ) {
		echo "$path:{$e->getLine()}: {$e->getMessage()}\n";
		++$bad;
	}
}
echo "PHP files: $n, parse errors: $bad\n";
exit( $bad ? 1 : 0 );
