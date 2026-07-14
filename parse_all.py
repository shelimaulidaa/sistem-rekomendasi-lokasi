from html.parser import HTMLParser

class MyHTMLParser(HTMLParser):
    def __init__(self):
        super().__init__()
        self.stack = []

    def handle_starttag(self, tag, attrs):
        if tag == 'div':
            self.stack.append(self.getpos()[0])

    def handle_endtag(self, tag):
        if tag == 'div':
            if self.stack:
                self.stack.pop()

    def close(self):
        super().close()
        for pos in self.stack:
            print(f"Unclosed div at line {pos}")

with open('resources/views/manajer/observasi/create.blade.php', 'r') as f:
    parser = MyHTMLParser()
    parser.feed(f.read())
    parser.close()
    print("Done")
